<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Smallbooks</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php
$loggedIn = Auth::check();
$user = Auth::user();
$currentRoute = $_GET['r'] ?? 'dashboard';
$navActive = function(array $routes) use ($currentRoute): string {
    return in_array($currentRoute, $routes, true) ? 'active' : '';
};
?>
<div class="app-shell <?= $loggedIn ? '' : 'no-sidebar' ?>">
  <?php if($loggedIn): ?>
  <aside class="sidebar">
    <div class="brand">
      <a class="brand-logo" href="<?=route('dashboard')?>" aria-label="Smallbooks Dashboard">
        <img src="../assets/img/smallbooks-logo.svg" alt="Smallbooks logo">
      </a>
    </div>
    <nav>
      <a class="<?= $navActive(['dashboard']) ?>" href="<?=route('dashboard')?>"><span class="mdi mdi-view-dashboard icon-blue"></span> Dashboard</a>
      <a class="<?= $navActive(['companies','company_new','company_select']) ?>" href="<?=route('companies')?>"><span class="mdi mdi-office-building icon-purple"></span> Companies</a>
      <?php if($company): ?>
      <a class="<?= $navActive(['accounts','account_new','account_edit','account_register']) ?>" href="<?=route('accounts')?>"><span class="mdi mdi-format-list-bulleted icon-teal"></span> Accounts</a>
      <a class="<?= $navActive(['customers','customer_new','customer_edit','customer_delete']) ?>" href="<?=route('customers')?>"><span class="mdi mdi-account-group icon-blue"></span> Customers</a>
      <a class="<?= $navActive(['vendors','vendor_new','vendor_edit','vendor_delete']) ?>" href="<?=route('vendors')?>"><span class="mdi mdi-truck icon-orange"></span> Vendors</a>
      <?php if(Auth::can('can_manage_transactions')): ?>
      <a class="<?= $navActive(['invoices','invoice_new','invoice_show','invoice_edit','invoice_delete','invoice_pay','invoice_print']) ?>" href="<?=route('invoices')?>"><span class="mdi mdi-file-document-edit icon-green"></span> Invoices</a>
      <a class="<?= $navActive(['bills','bill_new','bill_show','bill_edit','bill_delete','bill_pay']) ?>" href="<?=route('bills')?>"><span class="mdi mdi-receipt-text icon-red"></span> Bills</a>
      <a class="<?= $navActive(['journal','journal_new','journal_show','journal_edit','journal_delete']) ?>" href="<?=route('journal')?>"><span class="mdi mdi-book-edit icon-yellow"></span> Journal</a>
      <a class="<?= $navActive(['reconciliations','reconciliation_new','reconciliation_show','reconciliation_delete']) ?>" href="<?=route('reconciliations')?>"><span class="mdi mdi-bank-check icon-teal"></span> Reconcile</a>
      <a class="<?= $navActive(['periods','period_close','period_unlock']) ?>" href="<?=route('periods')?>"><span class="mdi mdi-lock-clock icon-red"></span> Periods</a>
      <?php endif; ?>
      <?php if(Auth::can('can_view_reports')): ?>
      <div class="nav-section">Reports</div>
      <a class="<?= $navActive(['reports_pl']) ?>" href="<?=route('reports_pl')?>"><span class="mdi mdi-chart-line icon-green"></span> Profit &amp; Loss</a>
      <a class="<?= $navActive(['reports_bs']) ?>" href="<?=route('reports_bs')?>"><span class="mdi mdi-scale-balance icon-purple"></span> Balance Sheet</a>
      <a class="<?= $navActive(['reports_customer']) ?>" href="<?=route('reports_customer')?>"><span class="mdi mdi-account-cash icon-blue"></span> Customers</a>
      <a class="<?= $navActive(['reports_vendor']) ?>" href="<?=route('reports_vendor')?>"><span class="mdi mdi-cart icon-orange"></span> Vendors</a>
      <a class="<?= $navActive(['audit_trail']) ?>" href="<?=route('audit_trail')?>"><span class="mdi mdi-history icon-purple"></span> Audit Trail</a>
      <?php endif; ?>
      <?php endif; ?>
      <div class="nav-section">Admin</div>
      <?php if(Auth::can('can_manage_users')): ?><a class="<?= $navActive(['users','user_new','user_edit','user_delete']) ?>" href="<?=route('users')?>"><span class="mdi mdi-account-cog icon-blue"></span> Users</a><?php endif; ?>
      <a href="<?=route('logout')?>"><span class="mdi mdi-logout icon-red"></span> Logout</a>
    </nav>
  </aside>
  <div class="mobile-nav-backdrop" onclick="document.body.classList.remove('nav-open')"></div>
  <?php endif; ?>
  <section class="workspace">
    <?php if($loggedIn): ?>
    <header class="topbar">
      <button class="mobile-nav-toggle" onclick="document.body.classList.toggle('nav-open')" aria-label="Open navigation"><span class="mdi mdi-menu"></span></button>
      <div class="topbar-spacer"></div>
      <a class="company-pill" href="<?=route('companies')?>"><span class="mdi mdi-domain"></span> <?= $company ? h($company['name']) : 'No company selected' ?></a>
      <div class="user-pill">
        <span class="avatar"><span class="mdi mdi-account"></span></span>
        <span class="user-copy"><strong><?= h($user['name'] ?? $user['email'] ?? 'User') ?></strong><small><?= !empty($user['is_superuser']) ? 'Superuser' : h($user['email'] ?? '') ?></small></span>
      </div>
    </header>
    <?php endif; ?>
    <main><?=$content?></main>
  </section>
</div>
</body>
</html>
