<div class="page-head">
  <h1><span class="mdi mdi-truck icon-orange"></span> Vendors</h1>
  <a class="button" href="<?=route('vendor_new')?>"><span class="mdi mdi-plus"></span> Add Vendor</a>
</div>
<?php if(!empty($_SESSION['flash_error'])): ?><p class="alert error"><?=h($_SESSION['flash_error']); unset($_SESSION['flash_error']);?></p><?php endif; ?>
<table>
  <tr><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th></tr>
  <?php foreach($vendors as $v):?>
  <tr>
    <td><?=h($v['name'])?></td>
    <td><?=h($v['email'])?></td>
    <td><?=h($v['phone'])?></td>
    <td><?=h($v['address'])?></td>
    <td class="actions">
      <a href="<?=route('vendor_edit',['id'=>$v['id']])?>"><span class="mdi mdi-pencil icon-blue"></span> Edit</a>
      <form class="inline" method="post" action="<?=route('vendor_delete')?>" onsubmit="return confirm('Delete this vendor? Vendors with bills cannot be deleted.');">
        <input type="hidden" name="csrf" value="<?=csrf_token()?>">
        <input type="hidden" name="id" value="<?=h($v['id'])?>">
        <button class="link danger"><span class="mdi mdi-delete"></span> Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach;?>
</table>
