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

$map = [
 'login'=>['AuthController','login'], 'logout'=>['AuthController','logout'], 'register'=>['AuthController','register'],
 'dashboard'=>['DashboardController','index'], 'companies'=>['CompanyController','index'], 'company_save'=>['CompanyController','save'], 'company_select'=>['CompanyController','select'],
 'accounts'=>['AccountController','index'], 'account_save'=>['AccountController','save'],
 'customers'=>['CustomerController','index'], 'customer_save'=>['CustomerController','save'],
 'vendors'=>['VendorController','index'], 'vendor_save'=>['VendorController','save'],
 'invoices'=>['InvoiceController','index'], 'invoice_new'=>['InvoiceController','create'], 'invoice_edit'=>['InvoiceController','edit'], 'invoice_save'=>['InvoiceController','save'], 'invoice_update'=>['InvoiceController','update'], 'invoice_delete'=>['InvoiceController','delete'], 'invoice_pay'=>['InvoiceController','pay'],
 'bills'=>['BillController','index'], 'bill_new'=>['BillController','create'], 'bill_edit'=>['BillController','edit'], 'bill_save'=>['BillController','save'], 'bill_update'=>['BillController','update'], 'bill_delete'=>['BillController','delete'], 'bill_pay'=>['BillController','pay'],
 'journal'=>['JournalController','index'], 'journal_new'=>['JournalController','create'], 'journal_edit'=>['JournalController','edit'], 'journal_save'=>['JournalController','save'], 'journal_update'=>['JournalController','update'], 'journal_delete'=>['JournalController','delete'], 'journal_view'=>['JournalController','view'],
 'reports_pl'=>['ReportController','profitLoss'], 'reports_bs'=>['ReportController','balanceSheet'], 'reports_customer'=>['ReportController','customer'], 'reports_vendor'=>['ReportController','vendor'],
];
if (!isset($map[$route])) die('Route not found');
[$class,$method] = $map[$route];
require __DIR__ . '/../app/Controllers/' . $class . '.php';
(new $class())->$method();
