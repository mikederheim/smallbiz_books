<?php
$status = $balance <= 0 ? 'Paid' : 'Amount Due';
?>
<div class="invoice-print screen-only">
  <p><a href="<?=route('invoices')?>">&larr; Back to invoices</a> <button onclick="window.print()"><span class="mdi mdi-printer icon-green"></span> Print invoice</button></p>
</div>

<section class="customer-invoice">
  <div class="invoice-top">
    <div>
      <h1>Invoice</h1>
      <p class="muted">Invoice #<?=h($invoice['invoice_number'])?></p>
    </div>
    <div class="invoice-company">
      <h2><?=h($company['name'] ?? 'Company')?></h2>
    </div>
  </div>

  <div class="invoice-meta-grid">
    <div class="invoice-box">
      <h3>Bill To</h3>
      <strong><?=h($invoice['customer_name'])?></strong><br>
      <?php if(!empty($invoice['customer_address'])): ?><?=nl2br(h($invoice['customer_address']))?><br><?php endif; ?>
      <?php if(!empty($invoice['customer_email'])): ?><?=h($invoice['customer_email'])?><br><?php endif; ?>
      <?php if(!empty($invoice['customer_phone'])): ?><?=h($invoice['customer_phone'])?><?php endif; ?>
    </div>
    <div class="invoice-box right">
      <table class="invoice-summary-table">
        <tr><th>Invoice Date</th><td><?=h($invoice['invoice_date'])?></td></tr>
        <tr><th>Due Date</th><td><?=h($invoice['due_date'])?></td></tr>
        <tr><th>Status</th><td><?=h(ucfirst($invoice['status']))?></td></tr>
      </table>
    </div>
  </div>

  <table class="invoice-lines">
    <thead><tr><th>Description</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td><?=!empty($invoice['notes']) ? nl2br(h($invoice['notes'])) : 'Services / products'?></td><td class="right"><?=$this->money($invoice['total'])?></td></tr>
    </tbody>
  </table>

  <div class="invoice-totals">
    <table>
      <tr><th>Subtotal</th><td><?=$this->money($invoice['total'])?></td></tr>
      <tr><th>Payments Received</th><td><?=$this->money($invoice['paid_amount'] ?? 0)?></td></tr>
      <tr class="invoice-balance"><th><?=h($status)?></th><td><?=$this->money(max(0,$balance))?></td></tr>
    </table>
  </div>

  <div class="invoice-footer">
    <p>Thank you for your business.</p>
  </div>
</section>
