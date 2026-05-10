<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_superadmin();
$id=(int)($_GET['id']??$_POST['id']??0); $stmt=$pdo->prepare('SELECT * FROM users WHERE id=?'); $stmt->execute([$id]); $u=$stmt->fetch(); if(!$u)die('Utente non trovato'); $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
$role=$_POST['role']==='superadmin'?'superadmin':'user'; $active=isset($_POST['active'])?1:0; $password=$_POST['password']??'';
if($password!==''){ if(strlen($password)<8)$error='Password almeno 8 caratteri.'; else $pdo->prepare('UPDATE users SET password=?, role=?, active=? WHERE id=?')->execute([password_hash($password,PASSWORD_DEFAULT),$role,$active,$id]); }
else $pdo->prepare('UPDATE users SET role=?, active=? WHERE id=?')->execute([$role,$active,$id]);
if(!$error){ header('Location: admin_users.php'); exit; }
}
require_once __DIR__ . '/header.php'; ?>
<h1 class="h3 mb-3">Modifica utente: <?= e($u['username']) ?></h1><?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card"><div class="card-body row g-3"><input type="hidden" name="id" value="<?= $id ?>"><div class="col-md-4"><label class="form-label">Nuova password</label><input type="password" class="form-control" name="password" placeholder="Lascia vuoto per non cambiare"></div><div class="col-md-4"><label class="form-label">Ruolo</label><select class="form-select" name="role"><option value="user" <?= $u['role']==='user'?'selected':'' ?>>user</option><option value="superadmin" <?= $u['role']==='superadmin'?'selected':'' ?>>superadmin</option></select></div><div class="col-md-4 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="active" id="active" <?= (int)$u['active']?'checked':'' ?>><label class="form-check-label" for="active">Utente attivo</label></div></div><div class="col-12"><button class="btn btn-primary">Salva</button> <a class="btn btn-secondary" href="admin_users.php">Annulla</a></div></div></form>
<?php require_once __DIR__ . '/footer.php'; ?>
