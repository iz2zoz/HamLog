<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_login();
$id=(int)($_GET['id']??$_POST['id']??0); $userId=current_user_id();
$stmt=$pdo->prepare('SELECT l.*, COUNT(q.id) AS qso_count FROM logs l LEFT JOIN qso q ON q.log_id=l.id AND q.user_id=l.user_id WHERE l.id=? AND l.user_id=? GROUP BY l.id');
$stmt->execute([$id,$userId]); $log=$stmt->fetch(); if(!$log){die('Log non trovato');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $confirm=trim($_POST['confirm']??'');
    if((int)$log['qso_count']>0 && $confirm !== 'ELIMINA') $error='Per eliminare un log non vuoto scrivi ELIMINA.';
    else { $pdo->prepare('DELETE FROM logs WHERE id=? AND user_id=?')->execute([$id,$userId]); header('Location: dashboard_logs.php'); exit; }
}
require_once __DIR__ . '/header.php';
?>
<h1 class="h3">Elimina log</h1>
<div class="alert alert-warning">Stai eliminando <strong><?= e($log['log_name']) ?></strong> con <?= (int)$log['qso_count'] ?> QSO.</div>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="id" value="<?= (int)$id ?>">
<?php if((int)$log['qso_count']>0): ?><label class="form-label">Scrivi ELIMINA per confermare</label><input class="form-control mb-3" name="confirm"><?php endif; ?>
<button class="btn btn-danger">Elimina</button> <a href="dashboard_logs.php" class="btn btn-secondary">Annulla</a>
</form>
<?php require_once __DIR__ . '/footer.php'; ?>
