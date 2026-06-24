<div class="page-head">
  <h1><span class="mdi mdi-account-cog icon-blue"></span> Users</h1>
  <a class="button" href="<?=route('user_new')?>"><span class="mdi mdi-account-plus"></span> Add User</a>
</div>
<?php if(!empty($_SESSION['flash_error'])): ?><p class="error"><?=h($_SESSION['flash_error'])?></p><?php unset($_SESSION['flash_error']); endif; ?>
<div class="card">
  <table>
    <thead><tr><th>Name</th><th>Email</th><th>Permissions</th><th>Created</th><th></th></tr></thead>
    <tbody>
    <?php foreach($users as $u): $isSuper = strtolower($u['email'])==='mike.derheim@mivent.com' || !empty($u['is_superuser']); ?>
      <tr>
        <td><?=h($u['name'])?><?= ((int)$u['id'] === $currentUserId) ? ' <span class="badge">You</span>' : '' ?><?= $isSuper ? ' <span class="badge super">Superuser</span>' : '' ?></td>
        <td><?=h($u['email'])?></td>
        <td class="muted">
          <?= !empty($u['can_manage_users']) || $isSuper ? 'Users ' : '' ?>
          <?= !empty($u['can_manage_companies']) || $isSuper ? 'Companies ' : '' ?>
          <?= !empty($u['can_manage_transactions']) || $isSuper ? 'Transactions ' : '' ?>
          <?= !empty($u['can_view_reports']) || $isSuper ? 'Reports' : '' ?>
        </td>
        <td><?=h(date('Y-m-d', strtotime($u['created_at'])))?></td>
        <td class="actions">
          <a href="<?=route('user_edit')?>&id=<?=(int)$u['id']?>"><span class="mdi mdi-pencil icon-blue"></span> Edit</a>
          <?php if((int)$u['id'] !== $currentUserId && count($users) > 1 && !$isSuper): ?>
            <form method="post" action="<?=route('user_delete')?>" style="display:inline" onsubmit="return confirm('Remove this user account?');">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="id" value="<?=(int)$u['id']?>">
              <button class="link danger"><span class="mdi mdi-delete"></span> Remove</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
