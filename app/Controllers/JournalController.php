<?php
class JournalController extends Controller {
    public function index(): void {
        $cid = $this->companyId();
        $stmt = $this->db->prepare('SELECT je.*, COALESCE(SUM(jl.debit),0) AS total_debits, COALESCE(SUM(jl.credit),0) AS total_credits, COUNT(jl.id) AS line_count FROM journal_entries je LEFT JOIN journal_lines jl ON jl.journal_entry_id = je.id WHERE je.company_id=? GROUP BY je.id ORDER BY je.entry_date DESC, je.id DESC LIMIT 200');
        $stmt->execute([$cid]);
        $entries = $stmt->fetchAll();
        $this->render('journal/index', compact('entries'));
    }
    public function create(): void { $accounts=$this->accounts($this->companyId()); $entry=null; $lines=[]; $this->render('journal/form', compact('accounts','entry','lines') + ['error'=>null]); }
    public function edit(): void { [$entry,$lines]=$this->loadEntry((int)($_GET['id']??0)); if(($entry['source_type']??'')!=='manual_journal') die('Edit the original invoice or bill for source-created journal entries.'); $accounts=$this->accounts($this->companyId()); $this->render('journal/form', compact('accounts','entry','lines') + ['error'=>null]); }
    public function view(): void { [$entry,$lines]=$this->loadEntry((int)($_GET['id']??0)); $this->render('journal/view', compact('entry','lines')); }
    public function save(): void { $this->persist(false); }
    public function update(): void { $this->persist(true); }
    public function delete(): void { check_csrf(); $cid=$this->companyId(); $id=(int)($_POST['id']??0); try{ $this->db->beginTransaction(); Ledger::deleteManualEntry($this->db,$cid,$id); $this->db->commit(); $this->redirect('journal'); }catch(Throwable $e){ if($this->db->inTransaction())$this->db->rollBack(); die($e->getMessage()); } }
    private function persist(bool $edit): void {
        check_csrf(); $cid=$this->companyId(); $accounts=$this->accounts($cid); $valid=array_map(fn($a)=>(int)$a['id'],$accounts); $entry=null; $id=(int)($_POST['id']??0);
        if($edit){ [$entry,] = $this->loadEntry($id); }
        $date=trim($_POST['entry_date']??''); $memo=trim($_POST['memo']??''); $posted=$_POST['lines']??[]; $lines=[];
        foreach($posted as $line){ $accountId=(int)($line['account_id']??0); $debit=round((float)($line['debit']??0),2); $credit=round((float)($line['credit']??0),2); $description=trim($line['description']??''); if($accountId===0&&$debit==0&&$credit==0&&$description==='')continue; if(!in_array($accountId,$valid,true)) { $this->formError($accounts,$entry,$posted,'One or more lines use an invalid account.'); return; } if($debit<0||$credit<0) { $this->formError($accounts,$entry,$posted,'Debits and credits cannot be negative.'); return; } if($debit>0&&$credit>0) { $this->formError($accounts,$entry,$posted,'A line cannot have both a debit and a credit.'); return; } if($debit==0&&$credit==0) { $this->formError($accounts,$entry,$posted,'Each line must have either a debit or a credit.'); return; } $lines[]=['account_id'=>$accountId,'debit'=>$debit,'credit'=>$credit,'description'=>$description?:null]; }
        if(!$date) { $this->formError($accounts,$entry,$posted,'Entry date is required.'); return; }
        try{ $this->db->beginTransaction(); if($edit) Ledger::replaceEntry($this->db,$cid,$id,$date,$memo,$lines); else Ledger::post($this->db,$cid,$date,$memo,$lines,'manual_journal',null); $this->db->commit(); $this->redirect('journal'); } catch(Throwable $e){ if($this->db->inTransaction())$this->db->rollBack(); $this->formError($accounts,$entry,$posted,$e->getMessage()); }
    }
    private function formError($accounts,$entry,$posted,$error): void { $lines=$posted; $this->render('journal/form', compact('accounts','entry','lines','error')); }
    private function loadEntry(int $id): array { $cid=$this->companyId(); $s=$this->db->prepare('SELECT * FROM journal_entries WHERE id=? AND company_id=?'); $s->execute([$id,$cid]); $entry=$s->fetch(); if(!$entry) die('Journal entry not found'); $s=$this->db->prepare('SELECT jl.*, a.code, a.name, a.type FROM journal_lines jl JOIN accounts a ON a.id=jl.account_id WHERE jl.journal_entry_id=? ORDER BY jl.id'); $s->execute([$id]); return [$entry,$s->fetchAll()]; }
    private function accounts(int $cid): array { $stmt=$this->db->prepare('SELECT * FROM accounts WHERE company_id=? AND is_active=1 ORDER BY code, name'); $stmt->execute([$cid]); return $stmt->fetchAll(); }
}
