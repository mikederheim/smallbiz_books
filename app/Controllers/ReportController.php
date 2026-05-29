<?php
class ReportController extends Controller {
 public function profitLoss(): void {
   $cid=$this->companyId(); $from=$_GET['from']??date('Y-01-01'); $to=$_GET['to']??date('Y-m-d');
   $rows=$this->reportRows($cid,$from,$to,['income','expense']);
   if(($_GET['export']??'')==='xls') $this->excel('profit_loss_'.$from.'_to_'.$to.'.xls','reports/profit_loss', compact('rows','from','to'));
   $this->render('reports/profit_loss', compact('rows','from','to'));
 }
 public function balanceSheet(): void {
   $cid=$this->companyId(); $to=$_GET['to']??date('Y-m-d');
   $rows=$this->reportRows($cid,'1900-01-01',$to,['asset','liability','equity']);
   if(($_GET['export']??'')==='xls') $this->excel('balance_sheet_as_of_'.$to.'.xls','reports/balance_sheet', compact('rows','to'));
   $this->render('reports/balance_sheet', compact('rows','to'));
 }
 public function customer(): void {
   $cid=$this->companyId();
   $s=$this->db->prepare('SELECT c.name, COUNT(i.id) invoices, COALESCE(SUM(i.total),0) revenue, MAX(i.invoice_date) last_invoice FROM customers c LEFT JOIN invoices i ON i.customer_id=c.id AND i.company_id=c.company_id WHERE c.company_id=? GROUP BY c.id ORDER BY revenue DESC');
   $s->execute([$cid]); $rows=$s->fetchAll();
   if(($_GET['export']??'')==='xls') $this->excel('customer_insights.xls','reports/customer', compact('rows'));
   $this->render('reports/customer', compact('rows'));
 }
 public function vendor(): void {
   $cid=$this->companyId();
   $s=$this->db->prepare('SELECT v.name, COUNT(b.id) bills, COALESCE(SUM(b.total),0) spend, MAX(b.bill_date) last_bill FROM vendors v LEFT JOIN bills b ON b.vendor_id=v.id AND b.company_id=v.company_id WHERE v.company_id=? GROUP BY v.id ORDER BY spend DESC');
   $s->execute([$cid]); $rows=$s->fetchAll();
   if(($_GET['export']??'')==='xls') $this->excel('vendor_insights.xls','reports/vendor', compact('rows'));
   $this->render('reports/vendor', compact('rows'));
 }
 private function reportRows($cid,$from,$to,$types){ $in=str_repeat('?,',count($types)-1).'?'; $s=$this->db->prepare("SELECT a.type,a.code,a.name, COALESCE(SUM(CASE WHEN je.company_id=? AND je.entry_date BETWEEN ? AND ? THEN jl.debit ELSE 0 END),0) debits, COALESCE(SUM(CASE WHEN je.company_id=? AND je.entry_date BETWEEN ? AND ? THEN jl.credit ELSE 0 END),0) credits FROM accounts a LEFT JOIN journal_lines jl ON jl.account_id=a.id LEFT JOIN journal_entries je ON je.id=jl.journal_entry_id WHERE a.company_id=? AND a.type IN ($in) GROUP BY a.id ORDER BY FIELD(a.type,'income','expense','asset','liability','equity'), a.code"); $s->execute(array_merge([$cid,$from,$to,$cid,$from,$to,$cid],$types)); return $s->fetchAll(); }
 private function excel($filename,$view,$data): void { header('Content-Type: application/vnd.ms-excel; charset=utf-8'); header('Content-Disposition: attachment; filename="'.$filename.'"'); extract($data); require __DIR__ . '/../Views/' . $view . '.php'; exit; }
}
