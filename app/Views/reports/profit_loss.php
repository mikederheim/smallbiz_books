<?php $isExport = (($_GET['export'] ?? '') === 'xls'); ?>
<div class="report-header">
  <div><h1><span class="mdi mdi-chart-line icon-green"></span> Profit &amp; Loss</h1><p><?=h($from)?> to <?=h($to)?></p></div>
  <?php if(!$isExport): ?><div class="screen-only"><button onclick="window.print()"><span class="mdi mdi-printer"></span> Print</button> <a class="button" href="<?=route('reports_pl')?>&from=<?=urlencode($from)?>&to=<?=urlencode($to)?>&export=xls"><span class="mdi mdi-file-excel"></span> Download Excel</a></div><?php endif; ?>
</div>
<?php if(!$isExport): ?><form class="screen-only"><input type="hidden" name="r" value="reports_pl">From <input type="date" name="from" value="<?=h($from)?>"> To <input type="date" name="to" value="<?=h($to)?>"><button><span class="mdi mdi-refresh"></span> Run</button></form><?php endif; ?>
<table class="report-table"><tr><th>Type</th><th>Account</th><th>Amount</th></tr>
<?php $net=0; $incomeTotal=0; $expenseTotal=0; foreach($rows as $r): $amt=$r['type']==='income' ? $r['credits']-$r['debits'] : $r['debits']-$r['credits']; if(abs((float)$amt)<0.005) continue; if($r['type']==='income'){ $incomeTotal+=$amt; $net+=$amt; } else { $expenseTotal+=$amt; $net-=$amt; } ?>
<tr><td><?=h(ucfirst($r['type']))?></td><td><?=h($r['code'].' '.$r['name'])?></td><td><?=$this->money($amt)?></td></tr>
<?php endforeach; ?>
<tr><th colspan="2">Total Income</th><th><?=$this->money($incomeTotal)?></th></tr>
<tr><th colspan="2">Total Expenses</th><th><?=$this->money($expenseTotal)?></th></tr>
<tr><th colspan="2">Net Income</th><th><?=$this->money($net)?></th></tr></table>
