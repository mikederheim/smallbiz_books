<h1><span class="mdi mdi-view-dashboard icon-blue"></span> Dashboard</h1>
<div class="cards">
  <div><span class="mdi mdi-bank kpi-icon icon-blue"></span>Bank Balance<strong><?=$this->money($bankBalance)?></strong><small>Checking account</small></div>
  <div><span class="mdi mdi-cash-plus kpi-icon icon-green"></span>Accounts Receivable<strong><?=$this->money($ar)?></strong></div>
  <div><span class="mdi mdi-cash-minus kpi-icon icon-red"></span>Accounts Payable<strong><?=$this->money($ap)?></strong></div>
  <div><span class="mdi mdi-trending-up kpi-icon icon-green"></span>Total Income<strong><?=$this->money($income)?></strong></div>
  <div><span class="mdi mdi-trending-down kpi-icon icon-orange"></span>Total Expenses<strong><?=$this->money($expense)?></strong></div>
</div>
<h2><span class="mdi mdi-calendar-month icon-purple"></span> Month-to-Date</h2>
<div class="cards">
  <div><span class="mdi mdi-cash-plus kpi-icon icon-green"></span>MTD Income<strong><?=$this->money($mtdIncome)?></strong></div>
  <div><span class="mdi mdi-cash-minus kpi-icon icon-orange"></span>MTD Expenses<strong><?=$this->money($mtdExpense)?></strong></div>
  <div><span class="mdi mdi-chart-areaspline kpi-icon icon-blue"></span>MTD Profit / Loss<strong><?=$this->money($mtdProfit)?></strong></div>
</div>
<h2><span class="mdi mdi-file-document-outline icon-green"></span> Recent Invoices</h2>
<table><tr><th>Date</th><th>#</th><th>Total</th><th>Status</th></tr><?php foreach($invoices as $i):?><tr><td><?=h($i['invoice_date'])?></td><td><?=h($i['invoice_number'])?></td><td><?=$this->money($i['total'])?></td><td><?=h($i['status'])?></td></tr><?php endforeach;?></table>
