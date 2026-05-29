<?php $isEdit=!empty($bill); ?>
<h1><?= $isEdit ? 'Edit Bill' : 'New Bill' ?></h1>
<p><a href="<?=route('bills')?>">&larr; Back to bills</a></p>
<form method="post" action="<?= $isEdit ? route('bill_update') : route('bill_save') ?>">
<input type="hidden" name="csrf" value="<?=csrf_token()?>"><?php if($isEdit):?><input type="hidden" name="id" value="<?=$bill['id']?>"><?php endif; ?>
<label>Vendor<select name="vendor_id"><?php foreach($vendors as $v):?><option value="<?=$v['id']?>" <?=((int)($bill['vendor_id']??0)===(int)$v['id'])?'selected':''?>><?=h($v['name'])?></option><?php endforeach;?></select></label>
<label>Bill #<input name="bill_number" value="<?=h($bill['bill_number'] ?? ('BILL-'.date('Ymd-His')))?>"></label>
<label>Bill date<input type="date" name="bill_date" value="<?=h($bill['bill_date'] ?? date('Y-m-d'))?>"></label>
<label>Due date<input type="date" name="due_date" value="<?=h($bill['due_date'] ?? date('Y-m-d', strtotime('+30 days')))?>"></label>
<label>Expense account<select name="expense_account_id"><?php foreach($expenses as $a):?><option value="<?=$a['id']?>" <?=((int)($expenseAccountId??0)===(int)$a['id'])?'selected':''?>><?=h($a['code'].' '.$a['name'])?></option><?php endforeach;?></select></label>
<label>Total<input type="number" step="0.01" name="total" value="<?=h($bill['total'] ?? '')?>" required></label>
<label>Notes<textarea name="notes"><?=h($bill['notes'] ?? '')?></textarea></label><button><?= $isEdit ? 'Update bill' : 'Save bill' ?></button></form>
