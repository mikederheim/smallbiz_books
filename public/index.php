<?php
session_start();
require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/Auth.php';
require __DIR__ . '/../app/Core/Controller.php';
require __DIR__ . '/../app/Core/Ledger.php';

$route = $_GET['r'] ?? 'dashboard';
$publicRoutes = ['login'];
if ($route === 'register') {
    // Allow public registration only for the very first user.
    // After that, creating additional users requires an active login.
    if (Auth::hasUsers()) Auth::requireLogin();
} elseif (!in_array($route, $publicRoutes, true)) {
    Auth::requireLogin();
}


$transactionRoutes = ['invoice_new','invoice_edit','invoice_save','invoice_update','invoice_delete','invoice_pay','bill_new','bill_edit','bill_save','bill_update','bill_delete','bill_pay','journal_new','journal_edit','journal_save','journal_update','journal_delete','reconcile_start','reconcile_save','reconcile_delete'];
$reportRoutes = ['reports_pl','reports_bs','reports_customer','reports_vendor'];
$userAdminRoutes = ['users','user_new','user_save','user_edit','user_update','user_delete'];
$companyAdminRoutes = ['company_save'];
if (in_array($route, $transactionRoutes, true)) Auth::requirePermission('can_manage_transactions');
if (in_array($route, $reportRoutes, true)) Auth::requirePermission('can_view_reports');
if (in_array($route, $userAdminRoutes, true)) Auth::requirePermission('can_manage_users');
if (in_array($route, $companyAdminRoutes, true)) Auth::requirePermission('can_manage_companies');

$map = [
 'login'=>['AuthController','login'], 'logout'=>['AuthController','logout'], 'register'=>['AuthController','register'],
 'users'=>['AuthController','users'], 'user_new'=>['AuthController','createUser'], 'user_save'=>['AuthController','saveUser'], 'user_edit'=>['AuthController','editUser'], 'user_update'=>['AuthController','updateUser'], 'user_delete'=>['AuthController','deleteUser'],
 'dashboard'=>['DashboardController','index'], 'companies'=>['CompanyController','index'], 'company_save'=>['CompanyController','save'], 'company_select'=>['CompanyController','select'],
 'accounts'=>['AccountController','index'], 'account_save'=>['AccountController','save'],
 'customers'=>['CustomerController','index'], 'customer_save'=>['CustomerController','save'],
 'vendors'=>['VendorController','index'], 'vendor_save'=>['VendorController','save'],
 'invoices'=>['InvoiceController','index'], 'invoice_new'=>['InvoiceController','create'], 'invoice_edit'=>['InvoiceController','edit'], 'invoice_save'=>['InvoiceController','save'], 'invoice_update'=>['InvoiceController','update'], 'invoice_delete'=>['InvoiceController','delete'], 'invoice_print'=>['InvoiceController','printable'], 'invoice_pay'=>['InvoiceController','pay'],
 'bills'=>['BillController','index'], 'bill_new'=>['BillController','create'], 'bill_edit'=>['BillController','edit'], 'bill_save'=>['BillController','save'], 'bill_update'=>['BillController','update'], 'bill_delete'=>['BillController','delete'], 'bill_pay'=>['BillController','pay'],
 'journal'=>['JournalController','index'], 'journal_new'=>['JournalController','create'], 'journal_edit'=>['JournalController','edit'], 'journal_save'=>['JournalController','save'], 'journal_update'=>['JournalController','update'], 'journal_delete'=>['JournalController','delete'], 'journal_view'=>['JournalController','view'],
 'reconciliations'=>['ReconciliationController','index'], 'reconcile_start'=>['ReconciliationController','start'], 'reconcile_save'=>['ReconciliationController','save'], 'reconcile_view'=>['ReconciliationController','view'], 'reconcile_delete'=>['ReconciliationController','delete'],
 'reports_pl'=>['ReportController','profitLoss'], 'reports_bs'=>['ReportController','balanceSheet'], 'reports_customer'=>['ReportController','customer'], 'reports_vendor'=>['ReportController','vendor'],
];
if (!isset($map[$route])) die('Route not found');
[$class,$method] = $map[$route];
require __DIR__ . '/../app/Controllers/' . $class . '.php';
(new $class())->$method();
