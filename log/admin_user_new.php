<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_superadmin();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
$username=trim($_POST['username']??''); $password=$_POST['password']??''; $role=$_POST['role']==='superadmin'?'superadmin':'user';
if($username===''||strlen($password)<8)$error='Username obbligatorio e password almeno 8 caratteri.'; else { try{$pdo->prepare('INSERT INTO users (username,password,role,active) VALUES (?,?,?,1)')->execute([$username,password_hash($password,PASSWORD_DEFAULT),$role]); header('Location: admin_users.php'); exit;}catch(PDOException $e){$error='Username già esistente.';} }
}
require_once __DIR__ . '/header.php'; ?>
<h1 class="h3 mb-3">Nuovo utente</h1><?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card"><div class="card-body row g-3"><div class="col-md-4"><label class="form-label">Username</label><input class="form-control" name="username" required autofocus></div><div class="col-md-4"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div><div class="col-md-4"><label class="form-label">Ruolo</label><select class="form-select" name="role"><option value="user">user</option><option value="superadmin">superadmin</option></select></div><div class="col-12"><button class="btn btn-primary">Crea</button> <a class="btn btn-secondary" href="admin_users.php">Annulla</a></div></div></form>
<?php require_once __DIR__ . '/footer.php'; ?>
