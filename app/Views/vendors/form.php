<div class="page-head">
  <h1><span class="mdi mdi-truck-cargo-container icon-orange"></span> <?= $mode === 'edit' ? 'Edit Vendor' : 'Add Vendor' ?></h1>
  <a class="button secondary" href="<?=route('vendors')?>"><span class="mdi mdi-arrow-left"></span> Back</a>
</div>
<form method="post" action="<?= $mode === 'edit' ? route('vendor_update') : route('vendor_save') ?>">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <?php if($mode === 'edit'): ?><input type="hidden" name="id" value="<?=h($vendor['id'])?>"><?php endif; ?>
  <label>Name<input name="name" value="<?=h($vendor['name'])?>" required></label>
  <label>Email<input name="email" type="email" value="<?=h($vendor['email'])?>"></label>
  <label>Phone<input name="phone" value="<?=h($vendor['phone'])?>"></label>
  <label>Address<textarea name="address" rows="4"><?=h($vendor['address'])?></textarea></label>
  <button><span class="mdi mdi-content-save"></span> Save Vendor</button>
</form>
