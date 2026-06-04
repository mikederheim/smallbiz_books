<div class="page-head">
  <h1><span class="mdi <?= $isEdit ? 'mdi-account-edit' : 'mdi-account-plus' ?> icon-blue"></span> <?= $isEdit ? 'Edit User' : 'Add User' ?></h1>
  <a class="button secondary" href="<?=route('users')?>"><span class="mdi mdi-arrow-left"></span> Back to Users</a>
</div>
<?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?>
<div class="card narrow">
  <form method="post" action="<?= $isEdit ? route('user_update') : route('user_save') ?>">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
    <?php if($isEdit): ?><input type="hidden" name="id" value="<?=(int)$user['id']?>"><?php endif; ?>
    <label>Name<input name="name" required value="<?=h($user['name'])?>"></label>
    <label>Email<input name="email" type="email" required value="<?=h($user['email'])?>"></label>
    <label><?= $isEdit ? 'New Password' : 'Password' ?>
      <input name="password" type="password" <?= $isEdit ? '' : 'required' ?> minlength="8">
    </label>
    <?php if($isEdit): ?><p class="muted">Leave password blank to keep the current password.</p><?php endif; ?>
    <button><span class="mdi mdi-content-save"></span> <?= $isEdit ? 'Save User' : 'Create User' ?></button>
  </form>
</div>
