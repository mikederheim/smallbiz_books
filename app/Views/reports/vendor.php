<?php $isExport = (($_GET['export'] ?? '') === 'xls'); ?>
<div class="report-header">
  <div><h1><span class="mdi mdi-truck icon-orange"></span> Vendor Insights</h1></div>
  <?php if(!$isExport): ?><div class="screen-only"><button onclick="window.print()"><span class="mdi mdi-printer"></span> Print</button> <a class="button" href="<?=route('reports_vendor')?>&export=xls"><span class="mdi mdi-file-excel"></span> Download Excel</a></div><?php endif; ?>
</div>
<table class="report-table"><tr><th>Vendor</th><th>Bills</th><th>Spend</th><th>Last bill</th></tr><?php foreach($rows as $r):?><tr><td><?=h($r['name'])?></td><td><?=h($r['bills'])?></td><td><?=$this->money($r['spend'])?></td><td><?=h($r['last_bill'])?></td></tr><?php endforeach;?></table>
