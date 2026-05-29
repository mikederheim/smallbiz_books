<?php $isEdit = !empty($entry); $existing = $lines ?: [['account_id'=>'','debit'=>'','credit'=>'','description'=>''],['account_id'=>'','debit'=>'','credit'=>'','description'=>'']]; ?>
<h1><?= $isEdit ? 'Edit Journal Entry' : 'New Journal Entry' ?></h1>
<p><a href="<?=route('journal')?>">&larr; Back to journal entries</a></p>
<?php if(!empty($error)): ?><p class="error"><?=h($error)?></p><?php endif; ?>
<form method="post" action="<?= $isEdit ? route('journal_update') : route('journal_save') ?>" class="wide-form">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <?php if($isEdit): ?><input type="hidden" name="id" value="<?=$entry['id']?>"><?php endif; ?>
  <label>Entry date<input type="date" name="entry_date" value="<?=h($entry['entry_date'] ?? date('Y-m-d'))?>" required></label>
  <label>Memo<input name="memo" value="<?=h($entry['memo'] ?? '')?>"></label>
  <table id="journal-lines">
    <tr><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th><th></th></tr>
    <?php foreach($existing as $idx=>$line): ?>
    <tr>
      <td><select name="lines[<?=$idx?>][account_id]"><option value="">Choose...</option><?php foreach($accounts as $a): ?><option value="<?=$a['id']?>" <?=((int)($line['account_id']??0)===(int)$a['id'])?'selected':''?>><?=h($a['code'].' '.$a['name'].' ('.$a['type'].')')?></option><?php endforeach; ?></select></td>
      <td><input name="lines[<?=$idx?>][description]" value="<?=h($line['description'] ?? '')?>"></td>
      <td><input class="debit" type="number" step="0.01" name="lines[<?=$idx?>][debit]" value="<?=h($line['debit'] ?? '')?>"></td>
      <td><input class="credit" type="number" step="0.01" name="lines[<?=$idx?>][credit]" value="<?=h($line['credit'] ?? '')?>"></td>
      <td><button type="button" onclick="this.closest('tr').remove(); updateTotals();">Remove</button></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <p><b>Total debits:</b> <span id="debit-total">$0.00</span> &nbsp; <b>Total credits:</b> <span id="credit-total">$0.00</span></p>
  <p><button type="button" onclick="addLine()">Add line</button> <button><?= $isEdit ? 'Update entry' : 'Post journal entry' ?></button></p>
</form>
<script>
let lineIndex = <?=count($existing)?>;
const accountOptions = `<?php foreach($accounts as $a): ?><option value="<?=$a['id']?>"><?=h($a['code'].' '.$a['name'].' ('.$a['type'].')')?></option><?php endforeach; ?>`;
function addLine(){ const table=document.getElementById('journal-lines'); const row=table.insertRow(-1); row.innerHTML = `<td><select name="lines[${lineIndex}][account_id]"><option value="">Choose...</option>${accountOptions}</select></td><td><input name="lines[${lineIndex}][description]"></td><td><input class="debit" type="number" step="0.01" name="lines[${lineIndex}][debit]"></td><td><input class="credit" type="number" step="0.01" name="lines[${lineIndex}][credit]"></td><td><button type="button" onclick="this.closest('tr').remove(); updateTotals();">Remove</button></td>`; lineIndex++; bindTotals(); }
function updateTotals(){ let d=0,c=0; document.querySelectorAll('.debit').forEach(i=>d+=parseFloat(i.value||0)); document.querySelectorAll('.credit').forEach(i=>c+=parseFloat(i.value||0)); document.getElementById('debit-total').textContent='$'+d.toFixed(2); document.getElementById('credit-total').textContent='$'+c.toFixed(2); }
function bindTotals(){ document.querySelectorAll('.debit,.credit').forEach(i=>i.oninput=updateTotals); updateTotals(); }
bindTotals();
</script>
