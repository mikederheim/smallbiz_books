<?php
class CompanyController extends Controller {
 public function index(): void {
   Auth::requireLogin();
   if (Auth::isSuperuser()) {
     $companies=$this->db->query('SELECT * FROM companies ORDER BY name')->fetchAll();
   } else {
     $stmt=$this->db->prepare('SELECT c.* FROM companies c JOIN user_company_access uca ON uca.company_id=c.id WHERE uca.user_id=? ORDER BY c.name');
     $stmt->execute([(int)Auth::user()['id']]);
     $companies=$stmt->fetchAll();
   }
   $canAddCompany = Auth::can('can_manage_companies');
   $this->render('companies/index', compact('companies','canAddCompany'));
 }
 public function save(): void {
   Auth::requirePermission('can_manage_companies');
   check_csrf();
   $stmt=$this->db->prepare('INSERT INTO companies(name, legal_name, tax_id, address) VALUES(?,?,?,?)');
   $stmt->execute([$_POST['name'],$_POST['legal_name']??'',$_POST['tax_id']??'',$_POST['address']??'']);
   $cid=(int)$this->db->lastInsertId();
   $_SESSION['company_id']=$cid;
   $this->seedAccounts($cid);
   $stmt=$this->db->prepare('INSERT IGNORE INTO user_company_access(user_id, company_id) VALUES(?,?)');
   $stmt->execute([(int)Auth::user()['id'], $cid]);
   $this->redirect('dashboard');
 }
 public function select(): void {
   Auth::requireLogin();
   $cid=(int)($_GET['id'] ?? 0);
   if (!Auth::canAccessCompany($cid)) die('Access denied');
   $_SESSION['company_id']=$cid;
   $this->redirect('dashboard');
 }
 private function seedAccounts(int $cid): void { $accounts=[['1000','Checking','asset'],['1100','Accounts Receivable','asset'],['1200','Inventory','asset'],['2000','Accounts Payable','liability'],['2100','Sales Tax Payable','liability'],['3000','Owner Equity','equity'],['4000','Sales Revenue','income'],['4100','Service Revenue','income'],['5000','Cost of Goods Sold','expense'],['6000','Advertising','expense'],['6100','Supplies','expense'],['6200','Utilities','expense'],['6300','Rent','expense'],['6900','Misc Expense','expense']]; $stmt=$this->db->prepare('INSERT INTO accounts(company_id, code, name, type) VALUES(?,?,?,?)'); foreach($accounts as $a) $stmt->execute([$cid,$a[0],$a[1],$a[2]]); }
}
