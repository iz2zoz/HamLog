<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_login();
$userId=current_user_id(); $id=(int)($_GET['id']??$_POST['id']??0);
$stmt=$pdo->prepare('SELECT * FROM qso WHERE id=? AND user_id=?'); $stmt->execute([$id,$userId]); $q=$stmt->fetch(); if(!$q){die('QSO non trovato');}
if($_SERVER['REQUEST_METHOD']==='POST'){ $pdo->prepare('DELETE FROM qso WHERE id=? AND user_id=?')->execute([$id,$userId]); header('Location: dashboard_qso.php?log_id='.(int)$q['log_id']); exit; }
require_once __DIR__ . '/header.php'; ?>
<h1 class="h3">Elimina QSO</h1><div class="alert alert-warning">Confermi eliminazione QSO con <strong><?= e($q['callsign']) ?></strong>?</div>
<form method="post"><input type="hidden" name="id" value="<?= $id ?>"><button class="btn btn-danger">Elimina</button> <a class="btn btn-secondary" href="edit_qso.php?id=<?= $id ?>">Annulla</a></form>
<?php require_once __DIR__ . '/footer.php'; ?>
