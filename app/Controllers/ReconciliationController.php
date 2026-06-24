<?php
class ReconciliationController extends Controller {
  public function index(): void {
    $cid = $this->companyId();
    $accounts = $this->bankAccounts($cid);
    $stmt = $this->db->prepare('SELECT r.*, a.name account_name, a.code account_code FROM bank_reconciliations r JOIN accounts a ON a.id=r.account_id WHERE r.company_id=? ORDER BY r.statement_date DESC, r.id DESC');
    $stmt->execute([$cid]);
    $reconciliations = $stmt->fetchAll();
    $this->render('reconciliations/index', compact('accounts','reconciliations'));
  }

  public function start(): void {
    $cid = $this->companyId();
    $accounts = $this->bankAccounts($cid);
    $accountId = (int)($_GET['account_id'] ?? ($accounts[0]['id'] ?? 0));
    $statementDate = $_GET['statement_date'] ?? date('Y-m-d');
    $beginningBalance = $this->lastEndingBalance($cid, $accountId);
    $endingBalance = $_GET['ending_balance'] ?? '';
    $transactions = $accountId ? $this->unreconciledTransactions($cid, $accountId, $statementDate) : [];
    $this->render('reconciliations/form', compact('accounts','accountId','statementDate','beginningBalance','endingBalance','transactions'));
  }

  public function save(): void {
    $cid = $this->companyId();
    $accountId = (int)($_POST['account_id'] ?? 0);
    $statementDate = $_POST['statement_date'] ?? date('Y-m-d');
    $beginningBalance = round((float)($_POST['beginning_balance'] ?? 0), 2);
    $endingBalance = round((float)($_POST['ending_balance'] ?? 0), 2);
    $cleared = array_map('intval', $_POST['cleared'] ?? []);
    $account = $this->bankAccount($cid, $accountId);
    if (!$account) die('Bank account not found for this company.');
    if (!$statementDate) die('Statement date is required.');

    $transactions = $this->unreconciledTransactions($cid, $accountId, $statementDate);
    $allowed = [];
    foreach ($transactions as $t) $allowed[(int)$t['journal_line_id']] = $t;

    $clearedDebit = 0.0; $clearedCredit = 0.0; $validCleared = [];
    foreach ($cleared as $lineId) {
      if (!isset($allowed[$lineId])) continue;
      $validCleared[] = $allowed[$lineId];
      $clearedDebit += (float)$allowed[$lineId]['debit'];
      $clearedCredit += (float)$allowed[$lineId]['credit'];
    }
    $clearedChange = round($clearedDebit - $clearedCredit, 2);
    $clearedBalance = round($beginningBalance + $clearedChange, 2);
    $difference = round($endingBalance - $clearedBalance, 2);
    if (abs($difference) > 0.009) {
      $_SESSION['flash_error'] = 'Reconciliation is not balanced. Difference: ' . $this->money($difference);
      header('Location: index.php?r=reconcile_start&account_id='.$accountId.'&statement_date='.urlencode($statementDate).'&ending_balance='.urlencode((string)$endingBalance));
      exit;
    }

    $this->db->beginTransaction();
    try {
      $stmt = $this->db->prepare('INSERT INTO bank_reconciliations(company_id, account_id, statement_date, beginning_balance, ending_balance, cleared_debits, cleared_credits) VALUES(?,?,?,?,?,?,?)');
      $stmt->execute([$cid,$accountId,$statementDate,$beginningBalance,$endingBalance,round($clearedDebit,2),round($clearedCredit,2)]);
      $recId = (int)$this->db->lastInsertId();
      $item = $this->db->prepare('INSERT INTO bank_reconciliation_items(reconciliation_id, journal_line_id) VALUES(?,?)');
      foreach ($validCleared as $t) $item->execute([$recId, (int)$t['journal_line_id']]);
      $this->db->commit();
      $this->redirect('reconciliations');
    } catch (Exception $e) {
      $this->db->rollBack();
      die('Unable to save reconciliation: ' . h($e->getMessage()));
    }
  }

  public function view(): void {
    $cid = $this->companyId();
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $this->db->prepare('SELECT r.*, a.name account_name, a.code account_code FROM bank_reconciliations r JOIN accounts a ON a.id=r.account_id WHERE r.company_id=? AND r.id=?');
    $stmt->execute([$cid,$id]);
    $reconciliation = $stmt->fetch();
    if (!$reconciliation) die('Reconciliation not found.');
    $items = $this->db->prepare('SELECT jl.id journal_line_id, je.entry_date, je.memo, je.source_type, je.source_id, jl.description, jl.debit, jl.credit FROM bank_reconciliation_items bri JOIN journal_lines jl ON jl.id=bri.journal_line_id JOIN journal_entries je ON je.id=jl.journal_entry_id WHERE bri.reconciliation_id=? ORDER BY je.entry_date, jl.id');
    $items->execute([$id]);
    $transactions = $items->fetchAll();
    $this->render('reconciliations/view', compact('reconciliation','transactions'));
  }

  public function delete(): void {
    $cid = $this->companyId();
    $id = (int)($_POST['id'] ?? 0);
    $this->db->prepare('DELETE FROM bank_reconciliations WHERE id=? AND company_id=?')->execute([$id,$cid]);
    $this->redirect('reconciliations');
  }

  private function bankAccounts(int $cid): array {
    $stmt = $this->db->prepare("SELECT * FROM accounts WHERE company_id=? AND type='asset' AND is_active=1 ORDER BY CASE WHEN name LIKE '%checking%' THEN 0 WHEN name LIKE '%bank%' THEN 1 ELSE 2 END, code, name");
    $stmt->execute([$cid]);
    return $stmt->fetchAll();
  }

  private function bankAccount(int $cid, int $accountId): ?array {
    $stmt = $this->db->prepare("SELECT * FROM accounts WHERE id=? AND company_id=? AND type='asset'");
    $stmt->execute([$accountId,$cid]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  private function lastEndingBalance(int $cid, int $accountId): float {
    if (!$accountId) return 0.0;
    $stmt = $this->db->prepare('SELECT ending_balance FROM bank_reconciliations WHERE company_id=? AND account_id=? ORDER BY statement_date DESC, id DESC LIMIT 1');
    $stmt->execute([$cid,$accountId]);
    $row = $stmt->fetch();
    return $row ? (float)$row['ending_balance'] : 0.0;
  }

  private function unreconciledTransactions(int $cid, int $accountId, string $statementDate): array {
    $stmt = $this->db->prepare("SELECT jl.id journal_line_id, je.entry_date, je.memo, je.source_type, je.source_id, jl.description, jl.debit, jl.credit
      FROM journal_lines jl
      JOIN journal_entries je ON je.id=jl.journal_entry_id
      LEFT JOIN bank_reconciliation_items bri ON bri.journal_line_id=jl.id
      WHERE je.company_id=? AND jl.account_id=? AND je.entry_date<=? AND bri.id IS NULL
      ORDER BY je.entry_date, jl.id");
    $stmt->execute([$cid,$accountId,$statementDate]);
    return $stmt->fetchAll();
  }
}
