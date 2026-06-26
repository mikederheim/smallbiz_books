<div class="page-head">
  <h1><span class="mdi mdi-account-group icon-blue"></span> Customers</h1>
  <a class="button" href="<?=route('customer_new')?>"><span class="mdi mdi-plus"></span> Add Customer</a>
</div>
<?php if(!empty($_SESSION['flash_error'])): ?><p class="alert error"><?=h($_SESSION['flash_error']); unset($_SESSION['flash_error']);?></p><?php endif; ?>
<table>
  <tr><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th></tr>
  <?php foreach($customers as $c):?>
  <tr>
    <td><?=h($c['name'])?></td>
    <td><?=h($c['email'])?></td>
    <td><?=h($c['phone'])?></td>
    <td><?=h($c['address'])?></td>
    <td class="actions">
      <a href="<?=route('customer_edit',['id'=>$c['id']])?>"><span class="mdi mdi-pencil icon-blue"></span> Edit</a>
      <form class="inline" method="post" action="<?=route('customer_delete')?>" onsubmit="return confirm('Delete this customer? Customers with invoices cannot be deleted.');">
        <input type="hidden" name="csrf" value="<?=csrf_token()?>">
        <input type="hidden" name="id" value="<?=h($c['id'])?>">
        <button class="link danger"><span class="mdi mdi-delete"></span> Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach;?>
</table>
