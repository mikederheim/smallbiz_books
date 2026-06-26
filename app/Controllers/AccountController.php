<?php
class AccountController extends Controller {
 public function index(): void {
  $cid=$this->companyId();
  $stmt=$this->db->prepare('SELECT * FROM accounts WHERE company_id=? ORDER BY code');
  $stmt->execute([$cid]);
  $accounts=$stmt->fetchAll();
  $this->render('accounts/index', compact('accounts'));
 }
 public function register(): void {
  $cid=$this->companyId();
  $id=(int)($_GET['id']??0);
  $stmt=$this->db->prepare('SELECT * FROM accounts WHERE id=? AND company_id=?');
  $stmt->execute([$id,$cid]);
  $account=$stmt->fetch();
  if(!$account) die('Account not found.');
  $from=$_GET['from'] ?? date('Y-01-01');
  $to=$_GET['to'] ?? date('Y-m-d');
  $openingStmt=$this->db->prepare('SELECT COALESCE(SUM(jl.debit-jl.credit),0) bal FROM journal_lines jl JOIN journal_entries je ON je.id=jl.journal_entry_id WHERE je.company_id=? AND jl.account_id=? AND je.entry_date < ?');
  $openingStmt->execute([$cid,$id,$from]);
  $opening=(float)$openingStmt->fetch()['bal'];
  $stmt=$this->db->prepare('SELECT je.id journal_entry_id, je.entry_date, je.memo, je.source_type, je.source_id, jl.description, jl.debit, jl.credit FROM journal_lines jl JOIN journal_entries je ON je.id=jl.journal_entry_id WHERE je.company_id=? AND jl.account_id=? AND je.entry_date BETWEEN ? AND ? ORDER BY je.entry_date, je.id, jl.id');
  $stmt->execute([$cid,$id,$from,$to]);
  $rows=$stmt->fetchAll();
  $balance=$opening;
  foreach($rows as &$row){ $balance += (float)$row['debit'] - (float)$row['credit']; $row['running_balance']=$balance; }
  unset($row);
  $this->render('accounts/register', compact('account','rows','from','to','opening','balance'));
 }
 public function save(): void {
  check_csrf();
  $cid=$this->companyId();
  $stmt=$this->db->prepare('INSERT INTO accounts(company_id, code, name, type) VALUES(?,?,?,?)');
  $stmt->execute([$cid,$_POST['code'],$_POST['name'],$_POST['type']]);
  AuditTrail::log($this->db,$cid,'created','account',(int)$this->db->lastInsertId(),'Created account '.$_POST['code'].' '.$_POST['name']);
  $this->redirect('accounts');
 }
}
