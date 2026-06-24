<?php $isMike = strtolower($user['email'] ?? '') === 'mike.derheim@mivent.com'; $isSuper = $isMike || !empty($user['is_superuser']); ?>
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
    <label>Email<input name="email" type="email" required value="<?=h($user['email'])?>" <?= $isMike ? 'readonly' : '' ?>></label>
    <label><?= $isEdit ? 'New Password' : 'Password' ?><input name="password" type="password" <?= $isEdit ? '' : 'required' ?> minlength="8"></label>
    <?php if($isEdit): ?><p class="muted">Leave password blank to keep the current password.</p><?php endif; ?>

    <h3><span class="mdi mdi-shield-account icon-purple"></span> Permissions</h3>
    <?php if($isMike): ?><p class="badge super">mike.derheim@mivent.com is always a superuser.</p><?php endif; ?>
    <label class="check-row"><input type="checkbox" name="is_superuser" value="1" <?= $isSuper ? 'checked' : '' ?> <?= $isMike ? 'disabled' : '' ?>> Superuser - can manage everything</label>
    <label class="check-row"><input type="checkbox" name="can_manage_users" value="1" <?= !empty($user['can_manage_users']) || $isSuper ? 'checked' : '' ?> <?= $isMike ? 'disabled' : '' ?>> Manage users and permissions</label>
    <label class="check-row"><input type="checkbox" name="can_manage_companies" value="1" <?= !empty($user['can_manage_companies']) || $isSuper ? 'checked' : '' ?> <?= $isMike ? 'disabled' : '' ?>> Add/manage companies</label>
    <label class="check-row"><input type="checkbox" name="can_manage_transactions" value="1" <?= !empty($user['can_manage_transactions']) || $isSuper ? 'checked' : '' ?> <?= $isMike ? 'disabled' : '' ?>> Add/edit/delete transactions</label>
    <label class="check-row"><input type="checkbox" name="can_view_reports" value="1" <?= !isset($user['can_view_reports']) || !empty($user['can_view_reports']) || $isSuper ? 'checked' : '' ?> <?= $isMike ? 'disabled' : '' ?>> View reports</label>

    <h3><span class="mdi mdi-domain icon-teal"></span> Company Access</h3>
    <p class="muted">Superusers can access all companies automatically.</p>
    <div class="company-checks">
      <?php foreach($companies as $c): ?>
        <label class="check-row"><input type="checkbox" name="company_ids[]" value="<?=(int)$c['id']?>" <?= $isSuper || in_array((int)$c['id'], $userCompanyIds, true) ? 'checked' : '' ?> <?= $isMike ? 'disabled' : '' ?>> <?=h($c['name'])?></label>
      <?php endforeach; ?>
      <?php if(!$companies): ?><p class="muted">No companies exist yet.</p><?php endif; ?>
    </div>
    <button><span class="mdi mdi-content-save"></span> <?= $isEdit ? 'Save User' : 'Create User' ?></button>
  </form>
</div>
