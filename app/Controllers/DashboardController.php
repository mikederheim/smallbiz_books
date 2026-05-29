<?php
class DashboardController extends Controller {
 public function index(): void { $cid=$this->companyId();
   $income=$this->sumType($cid,'income'); $expense=$this->sumType($cid,'expense'); $ar=$this->sumAccount($cid,'Accounts Receivable'); $ap=$this->sumAccount($cid,'Accounts Payable');
   $bankBalance=$this->sumAccount($cid,'Checking');
   $monthStart=date('Y-m-01'); $today=date('Y-m-d');
   $mtdIncome=$this->sumType($cid,'income',$monthStart,$today);
   $mtdExpense=$this->sumType($cid,'expense',$monthStart,$today);
   $mtdProfit=$mtdIncome-$mtdExpense;
   $recent=$this->db->prepare('SELECT * FROM invoices WHERE company_id=? ORDER BY invoice_date DESC LIMIT 5'); $recent->execute([$cid]); $invoices=$recent->fetchAll();
   $this->render('dashboard/index', compact('income','expense','ar','ap','bankBalance','mtdIncome','mtdExpense','mtdProfit','invoices'));
 }
 private function sumType($cid,$type,$from=null,$to=null){
   $dateSql=''; $params=[$cid,$type];
   if($from && $to){ $dateSql=' AND je.entry_date BETWEEN ? AND ?'; $params[]=$from; $params[]=$to; }
   $normal = $type==='expense' ? 'jl.debit-jl.credit' : 'jl.credit-jl.debit';
   $s=$this->db->prepare("SELECT COALESCE(SUM($normal),0) total FROM journal_lines jl JOIN journal_entries je ON je.id=jl.journal_entry_id JOIN accounts a ON a.id=jl.account_id WHERE je.company_id=? AND a.type=?$dateSql");
   $s->execute($params); return (float)$s->fetch()['total'];
 }
 private function sumAccount($cid,$name){ $s=$this->db->prepare('SELECT COALESCE(SUM(jl.debit-jl.credit),0) total FROM journal_lines jl JOIN journal_entries je ON je.id=jl.journal_entry_id JOIN accounts a ON a.id=jl.account_id WHERE je.company_id=? AND a.name=?'); $s->execute([$cid,$name]); return (float)$s->fetch()['total']; }
}
