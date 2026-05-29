<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <title><?= !empty($firstUser) ? 'Create First User' : 'Create User' ?></title>
</head>
<body>
<main class="auth">
  <h1><span class="mdi mdi-account-plus icon-blue"></span> <?= !empty($firstUser) ? 'Create First User' : 'Create User' ?></h1>
  <?php if(empty($firstUser)): ?>
    <p class="muted">Only logged-in users can create additional users.</p>
  <?php endif; ?>
  <?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
    <label>Name<input name="name" required></label>
    <label>Email<input name="email" type="email" required></label>
    <label>Password<input name="password" type="password" minlength="8" required></label>
    <button><span class="mdi mdi-account-plus"></span> Create User</button>
  </form>
  <?php if(Auth::check()): ?>
    <p><a href="<?=route('dashboard')?>">Back to dashboard</a></p>
  <?php else: ?>
    <p><a href="<?=route('login')?>">Back to login</a></p>
  <?php endif; ?>
</main>
</body>
</html>
