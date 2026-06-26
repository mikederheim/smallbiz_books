<?php $u = Auth::user(); $first = trim(explode(' ', (string)($u['name'] ?? ''))[0] ?? ''); if($first==='') $first = 'there'; ?>
<div class="dashboard-welcome">
  <div>
    <h1>Welcome back, <?=h($first)?>!</h1>
    <p>Here&rsquo;s what&rsquo;s happening with your business.</p>
  </div>
</div>
<div class="cards">
  <div><span class="mdi mdi-bank kpi-icon icon-blue"></span><small>Bank Balance</small><strong><?=$this->money($bankBalance)?></strong><small>Checking account</small></div>
  <div><span class="mdi mdi-cash-plus kpi-icon icon-green"></span><small>Accounts Receivable</small><strong><?=$this->money($ar)?></strong></div>
  <div><span class="mdi mdi-cash-minus kpi-icon icon-red"></span><small>Accounts Payable</small><strong><?=$this->money($ap)?></strong></div>
  <div><span class="mdi mdi-trending-up kpi-icon icon-green"></span><small>Total Income</small><strong><?=$this->money($income)?></strong></div>
  <div><span class="mdi mdi-trending-down kpi-icon icon-orange"></span><small>Total Expenses</small><strong><?=$this->money($expense)?></strong></div>
</div>
<h2><span class="mdi mdi-calendar-month icon-purple"></span> Month-to-Date</h2>
<div class="cards compact-cards">
  <div><span class="mdi mdi-cash-plus kpi-icon icon-green"></span><small>MTD Income</small><strong><?=$this->money($mtdIncome)?></strong></div>
  <div><span class="mdi mdi-cash-minus kpi-icon icon-orange"></span><small>MTD Expenses</small><strong><?=$this->money($mtdExpense)?></strong></div>
  <div><span class="mdi mdi-chart-areaspline kpi-icon icon-blue"></span><small>MTD Profit / Loss</small><strong><?=$this->money($mtdProfit)?></strong></div>
</div>
<div class="card">
  <h2><span class="mdi mdi-file-document-outline icon-green"></span> Recent Invoices</h2>
  <table><tr><th>Date</th><th>#</th><th>Total</th><th>Status</th></tr><?php foreach($invoices as $i):?><tr><td><?=h($i['invoice_date'])?></td><td><?=h($i['invoice_number'])?></td><td><?=$this->money($i['total'])?></td><td><?=h($i['status'])?></td></tr><?php endforeach;?></table>
</div>
