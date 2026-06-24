<h1><span class="mdi mdi-bank-check icon-teal"></span> Reconciliation Detail</h1>
<div class="report-header">
  <div>
    <h2><?=h($reconciliation['account_code'].' '.$reconciliation['account_name'])?></h2>
    <p>Statement date: <?=h($reconciliation['statement_date'])?></p>
  </div>
  <div class="screen-only"><button onclick="window.print()"><span class="mdi mdi-printer"></span> Print</button></div>
</div>
<div class="cards compact-cards">
  <div>Beginning Balance<strong><?=$this->money($reconciliation['beginning_balance'])?></strong></div>
  <div>Ending Balance<strong><?=$this->money($reconciliation['ending_balance'])?></strong></div>
  <div>Cleared Debits<strong><?=$this->money($reconciliation['cleared_debits'])?></strong></div>
  <div>Cleared Credits<strong><?=$this->money($reconciliation['cleared_credits'])?></strong></div>
</div>
<table>
  <tr><th>Date</th><th>Memo</th><th>Source</th><th>Deposit / Debit</th><th>Withdrawal / Credit</th></tr>
  <?php foreach($transactions as $t): ?>
    <tr><td><?=h($t['entry_date'])?></td><td><?=h($t['memo'] ?: $t['description'])?></td><td><?=h($t['source_type'] ?: 'manual')?></td><td><?=$this->money($t['debit'])?></td><td><?=$this->money($t['credit'])?></td></tr>
  <?php endforeach; ?>
</table>
<form class="screen-only" method="post" action="<?=route('reconcile_delete')?>" onsubmit="return confirm('Delete this reconciliation? This will make the cleared transactions available to reconcile again.');">
  <input type="hidden" name="id" value="<?=$reconciliation['id']?>">
  <button class="danger"><span class="mdi mdi-delete"></span> Delete Reconciliation</button>
  <a class="button secondary" href="<?=route('reconciliations')?>">Back</a>
</form>
