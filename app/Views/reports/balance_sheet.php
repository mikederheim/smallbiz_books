<?php $isExport = (($_GET['export'] ?? '') === 'xls'); ?>
<div class="report-header">
  <div><h1><span class="mdi mdi-scale-balance icon-purple"></span> Balance Sheet</h1><p>As of <?=h($to)?></p></div>
  <?php if(!$isExport): ?><div class="screen-only"><button onclick="window.print()"><span class="mdi mdi-printer"></span> Print</button> <a class="button" href="<?=route('reports_bs')?>&to=<?=urlencode($to)?>&export=xls"><span class="mdi mdi-file-excel"></span> Download Excel</a></div><?php endif; ?>
</div>
<?php if(!$isExport): ?><form class="screen-only"><input type="hidden" name="r" value="reports_bs">As of <input type="date" name="to" value="<?=h($to)?>"><button><span class="mdi mdi-refresh"></span> Run</button></form><?php endif; ?>
<table class="report-table"><tr><th>Type</th><th>Account</th><th>Balance</th></tr><?php foreach($rows as $r): $normal=in_array($r['type'],['asset','expense']); $amt=$normal ? $r['debits']-$r['credits'] : $r['credits']-$r['debits']; ?><tr><td><?=h(ucfirst($r['type']))?></td><td><?=h($r['code'].' '.$r['name'])?></td><td><?=$this->money($amt)?></td></tr><?php endforeach;?></table>
