<?php $err=$_SESSION['flash_error']??null; unset($_SESSION['flash_error']); ?>
<h1><span class="mdi mdi-bank-check icon-teal"></span> Bank Reconciliations</h1>
<?php if($err): ?><div class="alert error"><?=h($err)?></div><?php endif; ?>
<div class="cardish">
  <h2><span class="mdi mdi-play-circle icon-green"></span> Start a Reconciliation</h2>
  <?php if(!$accounts): ?>
    <p>No asset accounts are available. Create a bank/checking asset account first.</p>
  <?php else: ?>
  <form method="get" action="index.php" class="inline-form">
    <input type="hidden" name="r" value="reconcile_start">
    <label>Bank account
      <select name="account_id">
        <?php foreach($accounts as $a): ?><option value="<?=$a['id']?>"><?=h($a['code'].' '.$a['name'])?></option><?php endforeach; ?>
      </select>
    </label>
    <label>Statement date <input type="date" name="statement_date" value="<?=date('Y-m-d')?>"></label>
    <button><span class="mdi mdi-arrow-right-bold"></span> Continue</button>
  </form>
  <?php endif; ?>
</div>
<h2><span class="mdi mdi-history icon-blue"></span> Completed Reconciliations</h2>
<table>
  <tr><th>Date</th><th>Account</th><th>Beginning</th><th>Ending</th><th>Cleared Debits</th><th>Cleared Credits</th><th>Action</th></tr>
  <?php foreach($reconciliations as $r): ?>
    <tr>
      <td><?=h($r['statement_date'])?></td>
      <td><?=h($r['account_code'].' '.$r['account_name'])?></td>
      <td><?=$this->money($r['beginning_balance'])?></td>
      <td><?=$this->money($r['ending_balance'])?></td>
      <td><?=$this->money($r['cleared_debits'])?></td>
      <td><?=$this->money($r['cleared_credits'])?></td>
      <td><a class="button small" href="<?=route('reconcile_view')?>&id=<?=$r['id']?>"><span class="mdi mdi-eye"></span> View</a></td>
    </tr>
  <?php endforeach; ?>
</table>
