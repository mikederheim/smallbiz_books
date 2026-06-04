<div class="page-head">
  <h1><span class="mdi mdi-account-cog icon-blue"></span> Users</h1>
  <a class="button" href="<?=route('user_new')?>"><span class="mdi mdi-account-plus"></span> Add User</a>
</div>
<?php if(!empty($_SESSION['flash_error'])): ?>
  <p class="error"><?=h($_SESSION['flash_error'])?></p>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<div class="card">
  <table>
    <thead>
      <tr><th>Name</th><th>Email</th><th>Created</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach($users as $u): ?>
      <tr>
        <td><?=h($u['name'])?><?= ((int)$u['id'] === $currentUserId) ? ' <span class="badge">You</span>' : '' ?></td>
        <td><?=h($u['email'])?></td>
        <td><?=h(date('Y-m-d', strtotime($u['created_at'])))?></td>
        <td class="actions">
          <a href="<?=route('user_edit')?>&id=<?=(int)$u['id']?>"><span class="mdi mdi-pencil icon-blue"></span> Edit</a>
          <?php if((int)$u['id'] !== $currentUserId && count($users) > 1): ?>
            <form method="post" action="<?=route('user_delete')?>" style="display:inline" onsubmit="return confirm('Remove this user account?');">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <input type="hidden" name="id" value="<?=(int)$u['id']?>">
              <button class="link danger"><span class="mdi mdi-delete"></span> Remove</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
