<?php $err=$_SESSION['flash_error']??null; unset($_SESSION['flash_error']); ?>
<h1><span class="mdi mdi-bank-check icon-teal"></span> Reconcile Account</h1>
<?php if($err): ?><div class="alert error"><?=h($err)?></div><?php endif; ?>
<form method="get" action="index.php" class="screen-only toolbar-form">
  <input type="hidden" name="r" value="reconcile_start">
  <label>Account
    <select name="account_id">
      <?php foreach($accounts as $a): ?><option value="<?=$a['id']?>" <?=$accountId==(int)$a['id']?'selected':''?>><?=h($a['code'].' '.$a['name'])?></option><?php endforeach; ?>
    </select>
  </label>
  <label>Statement date <input type="date" name="statement_date" value="<?=h($statementDate)?>"></label>
  <label>Ending balance <input type="number" step="0.01" name="ending_balance" value="<?=h((string)$endingBalance)?>"></label>
  <button><span class="mdi mdi-refresh"></span> Load</button>
</form>
<form method="post" action="<?=route('reconcile_save')?>" id="reconcileForm">
  <input type="hidden" name="account_id" value="<?=$accountId?>">
  <input type="hidden" name="statement_date" value="<?=h($statementDate)?>">
  <div class="cards compact-cards">
    <div><span class="mdi mdi-calendar-start kpi-icon icon-blue"></span>Beginning Balance<strong id="beginningDisplay"><?=$this->money($beginningBalance)?></strong><input type="hidden" name="beginning_balance" id="beginningBalance" value="<?=h(number_format((float)$beginningBalance,2,'.',''))?>"></div>
    <div><span class="mdi mdi-calendar-end kpi-icon icon-purple"></span>Ending Balance<strong><input class="money-input" type="number" step="0.01" name="ending_balance" id="endingBalance" value="<?=h((string)$endingBalance)?>" required></strong></div>
    <div><span class="mdi mdi-check-circle kpi-icon icon-green"></span>Cleared Balance<strong id="clearedBalance">$0.00</strong></div>
    <div><span class="mdi mdi-calculator kpi-icon icon-orange"></span>Difference<strong id="difference">$0.00</strong></div>
  </div>
  <p class="muted">Check transactions that appear on the bank statement. The reconciliation can be saved only when the difference is $0.00.</p>
  <table>
    <tr><th>Clear</th><th>Date</th><th>Memo</th><th>Source</th><th>Deposit / Debit</th><th>Withdrawal / Credit</th></tr>
    <?php foreach($transactions as $t): ?>
      <tr>
        <td><input class="clear-box" type="checkbox" name="cleared[]" value="<?=$t['journal_line_id']?>" data-debit="<?=h($t['debit'])?>" data-credit="<?=h($t['credit'])?>"></td>
        <td><?=h($t['entry_date'])?></td>
        <td><?=h($t['memo'] ?: $t['description'])?></td>
        <td><?=h($t['source_type'] ?: 'manual')?></td>
        <td><?=$this->money($t['debit'])?></td>
        <td><?=$this->money($t['credit'])?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p><button><span class="mdi mdi-content-save-check"></span> Save Completed Reconciliation</button> <a class="button secondary" href="<?=route('reconciliations')?>">Cancel</a></p>
</form>
<script>
function money(n){ return '$' + Number(n || 0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); }
function recalc(){
  const beginning = parseFloat(document.getElementById('beginningBalance').value || '0');
  const ending = parseFloat(document.getElementById('endingBalance').value || '0');
  let debits=0, credits=0;
  document.querySelectorAll('.clear-box:checked').forEach(cb=>{ debits += parseFloat(cb.dataset.debit||'0'); credits += parseFloat(cb.dataset.credit||'0'); });
  const clearedBalance = beginning + debits - credits;
  const diff = ending - clearedBalance;
  document.getElementById('clearedBalance').textContent = money(clearedBalance);
  document.getElementById('difference').textContent = money(diff);
}
document.querySelectorAll('.clear-box').forEach(cb=>cb.addEventListener('change', recalc));
document.getElementById('endingBalance').addEventListener('input', recalc);
recalc();
</script>
