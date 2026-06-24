<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=h($config['app_name'])?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $loggedIn = Auth::check(); ?>
<?php if($loggedIn): ?>
<button class="mobile-nav-toggle" onclick="document.body.classList.toggle('nav-open')"><span class="mdi mdi-menu"></span> Menu</button>
<?php endif; ?>
<div class="app-shell <?= $loggedIn ? '' : 'no-sidebar' ?>">
  <?php if($loggedIn): ?>
  <aside class="sidebar">
    <div class="brand">
      <b><span class="mdi mdi-book-open-variant"></span> <?=h($config['app_name'])?></b>
      <span><span class="mdi mdi-domain"></span> <?= $company ? h($company['name']) : 'No company selected' ?></span>
    </div>
    <nav>
      <a href="<?=route('dashboard')?>"><span class="mdi mdi-view-dashboard icon-blue"></span> Dashboard</a>
      <a href="<?=route('companies')?>"><span class="mdi mdi-office-building icon-purple"></span> Companies</a>
      <?php if($company): ?>
      <a href="<?=route('accounts')?>"><span class="mdi mdi-format-list-bulleted icon-teal"></span> Accounts</a>
      <a href="<?=route('customers')?>"><span class="mdi mdi-account-group icon-blue"></span> Customers</a>
      <a href="<?=route('vendors')?>"><span class="mdi mdi-truck icon-orange"></span> Vendors</a>
      <?php if(Auth::can('can_manage_transactions')): ?>
      <a href="<?=route('invoices')?>"><span class="mdi mdi-file-document-edit icon-green"></span> Invoices</a>
      <a href="<?=route('bills')?>"><span class="mdi mdi-receipt-text icon-red"></span> Bills</a>
      <a href="<?=route('journal')?>"><span class="mdi mdi-book-edit icon-yellow"></span> Journal</a>
      <a href="<?=route('reconciliations')?>"><span class="mdi mdi-bank-check icon-teal"></span> Reconcile</a>
      <?php endif; ?>
      <?php if(Auth::can('can_view_reports')): ?>
      <div class="nav-section">Reports</div>
      <a href="<?=route('reports_pl')?>"><span class="mdi mdi-chart-line icon-green"></span> Profit &amp; Loss</a>
      <a href="<?=route('reports_bs')?>"><span class="mdi mdi-scale-balance icon-purple"></span> Balance Sheet</a>
      <a href="<?=route('reports_customer')?>"><span class="mdi mdi-account-cash icon-blue"></span> Customers</a>
      <a href="<?=route('reports_vendor')?>"><span class="mdi mdi-cart icon-orange"></span> Vendors</a>
      <?php endif; ?>
      <?php endif; ?>
      <div class="nav-section">Admin</div>
      <?php if(Auth::can('can_manage_users')): ?><a href="<?=route('users')?>"><span class="mdi mdi-account-cog icon-blue"></span> Users</a><?php endif; ?>
      <a href="<?=route('logout')?>"><span class="mdi mdi-logout icon-red"></span> Logout</a>
    </nav>
  </aside>
  <div class="mobile-nav-backdrop" onclick="document.body.classList.remove('nav-open')"></div>
  <?php endif; ?>
  <main><?=$content?></main>
</div>
</body>
</html>
