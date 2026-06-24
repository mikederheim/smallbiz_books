<div class="page-head"><h1><span class="mdi mdi-office-building icon-purple"></span> Companies</h1></div>
<div class="card"><table><tr><th>Name</th><th>Legal name</th><th></th></tr><?php foreach($companies as $c):?><tr><td><?=h($c['name'])?></td><td><?=h($c['legal_name'])?></td><td><a href="<?=route('company_select')?>&id=<?=$c['id']?>"><span class="mdi mdi-check-circle icon-green"></span> Use</a></td></tr><?php endforeach;?></table></div>
<?php if($canAddCompany): ?>
  <h2><span class="mdi mdi-plus-circle icon-green"></span> Add company</h2>
  <form method="post" action="<?=route('company_save')?>"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><label>Name<input name="name" required></label><label>Legal name<input name="legal_name"></label><label>Tax ID<input name="tax_id"></label><label>Address<textarea name="address"></textarea></label><button><span class="mdi mdi-content-save"></span> Save</button></form>
<?php endif; ?>
