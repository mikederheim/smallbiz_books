<?php $isEdit=!empty($invoice); ?>
<h1><?= $isEdit ? 'Edit Invoice' : 'New Invoice' ?></h1>
<p><a href="<?=route('invoices')?>">&larr; Back to invoices</a></p>
<form method="post" action="<?= $isEdit ? route('invoice_update') : route('invoice_save') ?>">
<input type="hidden" name="csrf" value="<?=csrf_token()?>"><?php if($isEdit):?><input type="hidden" name="id" value="<?=$invoice['id']?>"><?php endif; ?>
<label>Customer<select name="customer_id"><?php foreach($customers as $c):?><option value="<?=$c['id']?>" <?=((int)($invoice['customer_id']??0)===(int)$c['id'])?'selected':''?>><?=h($c['name'])?></option><?php endforeach;?></select></label>
<label>Invoice #<input name="invoice_number" value="<?=h($invoice['invoice_number'] ?? ('INV-'.date('Ymd-His')))?>"></label>
<label>Invoice date<input type="date" name="invoice_date" value="<?=h($invoice['invoice_date'] ?? date('Y-m-d'))?>"></label>
<label>Due date<input type="date" name="due_date" value="<?=h($invoice['due_date'] ?? date('Y-m-d', strtotime('+30 days')))?>"></label>
<label>Income account<select name="income_account_id"><?php foreach($income as $a):?><option value="<?=$a['id']?>" <?=((int)($incomeAccountId??0)===(int)$a['id'])?'selected':''?>><?=h($a['code'].' '.$a['name'])?></option><?php endforeach;?></select></label>
<label>Total<input type="number" step="0.01" name="total" value="<?=h($invoice['total'] ?? '')?>" required></label>
<label>Notes<textarea name="notes"><?=h($invoice['notes'] ?? '')?></textarea></label><button><?= $isEdit ? 'Update invoice' : 'Save invoice' ?></button></form>
