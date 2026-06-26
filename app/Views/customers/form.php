<div class="page-head">
  <h1><span class="mdi mdi-account-edit icon-blue"></span> <?= $mode === 'edit' ? 'Edit Customer' : 'Add Customer' ?></h1>
  <a class="button secondary" href="<?=route('customers')?>"><span class="mdi mdi-arrow-left"></span> Back</a>
</div>
<form method="post" action="<?= $mode === 'edit' ? route('customer_update') : route('customer_save') ?>">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <?php if($mode === 'edit'): ?><input type="hidden" name="id" value="<?=h($customer['id'])?>"><?php endif; ?>
  <label>Name<input name="name" value="<?=h($customer['name'])?>" required></label>
  <label>Email<input name="email" type="email" value="<?=h($customer['email'])?>"></label>
  <label>Phone<input name="phone" value="<?=h($customer['phone'])?>"></label>
  <label>Address<textarea name="address" rows="4"><?=h($customer['address'])?></textarea></label>
  <button><span class="mdi mdi-content-save"></span> Save Customer</button>
</form>
