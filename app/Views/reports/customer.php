<?php $isExport = (($_GET['export'] ?? '') === 'xls'); ?>
<div class="report-header">
  <div><h1><span class="mdi mdi-account-group icon-blue"></span> Customer Insights</h1></div>
  <?php if(!$isExport): ?><div class="screen-only"><button onclick="window.print()"><span class="mdi mdi-printer"></span> Print</button> <a class="button" href="<?=route('reports_customer')?>&export=xls"><span class="mdi mdi-file-excel"></span> Download Excel</a></div><?php endif; ?>
</div>
<table class="report-table"><tr><th>Customer</th><th>Invoices</th><th>Revenue</th><th>Last invoice</th></tr><?php foreach($rows as $r):?><tr><td><?=h($r['name'])?></td><td><?=h($r['invoices'])?></td><td><?=$this->money($r['revenue'])?></td><td><?=h($r['last_invoice'])?></td></tr><?php endforeach;?></table>
