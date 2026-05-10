<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_login();
$userId=current_user_id(); $id=(int)($_GET['id']??$_POST['id']??0);
$stmt=$pdo->prepare('SELECT q.*, l.timezone FROM qso q JOIN logs l ON l.id=q.log_id AND l.user_id=q.user_id WHERE q.id=? AND q.user_id=?'); $stmt->execute([$id,$userId]); $q=$stmt->fetch(); if(!$q){die('QSO non trovato');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $callsign=normalize_call($_POST['callsign']??''); $band=valid_choice($_POST['band']??'20m', bands(), '20m'); $mode=valid_choice($_POST['mode']??'SSB', modes(), 'SSB');
    $freq=trim($_POST['freq']??''); $freqVal=$freq===''?null:str_replace(',','.',$freq);
    $tz=new DateTimeZone($q['timezone'] ?: 'UTC'); $local=new DateTime(($_POST['local_date']??date('Y-m-d')).' '.($_POST['local_time']??date('H:i:s')), $tz); $utc=clone $local; $utc->setTimezone(new DateTimeZone('UTC'));
    if($callsign==='') $error='Call obbligatorio.';
    else { $stmt=$pdo->prepare('UPDATE qso SET callsign=?, band=?, mode=?, freq=?, rst_sent=?, rst_rcvd=?, name=?, qth=?, gridsquare=?, notes=?, qso_datetime_utc=?, qso_datetime_local=? WHERE id=? AND user_id=?');
        $stmt->execute([$callsign,$band,$mode,$freqVal,trim($_POST['rst_sent']??''),trim($_POST['rst_rcvd']??''),trim($_POST['name']??''),trim($_POST['qth']??''),strtoupper(trim($_POST['gridsquare']??'')),trim($_POST['notes']??''),$utc->format('Y-m-d H:i:s'),$local->format('Y-m-d H:i:s'),$id,$userId]);
        header('Location: dashboard_qso.php?log_id='.(int)$q['log_id']); exit; }
}
$localDate=substr($q['qso_datetime_local'],0,10); $localTime=substr($q['qso_datetime_local'],11,8);
require_once __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Modifica QSO</h1><?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card"><div class="card-body row g-3"><input type="hidden" name="id" value="<?= $id ?>">
<div class="col-md-3"><label class="form-label">Call</label><input class="form-control call-input" name="callsign" value="<?= e($q['callsign']) ?>" required autofocus></div>
<div class="col-md-2"><label class="form-label">Banda</label><select name="band" class="form-select"><?php foreach(bands() as $b): ?><option <?= $q['band']===$b?'selected':'' ?>><?= e($b) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label">Modo</label><select name="mode" class="form-select"><?php foreach(modes() as $m): ?><option <?= $q['mode']===$m?'selected':'' ?>><?= e($m) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label">RST S</label><input class="form-control" name="rst_sent" value="<?= e($q['rst_sent']) ?>"></div>
<div class="col-md-2"><label class="form-label">RST R</label><input class="form-control" name="rst_rcvd" value="<?= e($q['rst_rcvd']) ?>"></div>
<div class="col-md-2"><label class="form-label">Freq</label><input class="form-control" name="freq" value="<?= e($q['freq']) ?>"></div>
<div class="col-md-3"><label class="form-label">Data locale</label><input type="date" class="form-control" name="local_date" value="<?= e($localDate) ?>"></div>
<div class="col-md-3"><label class="form-label">Ora locale</label><input type="time" step="1" class="form-control" name="local_time" value="<?= e($localTime) ?>"></div>
<div class="col-md-3"><label class="form-label">Nome</label><input class="form-control" name="name" value="<?= e($q['name']) ?>"></div>
<div class="col-md-3"><label class="form-label">QTH</label><input class="form-control" name="qth" value="<?= e($q['qth']) ?>"></div>
<div class="col-md-3"><label class="form-label">Locator corrispondente</label><input class="form-control call-input" name="gridsquare" value="<?= e($q['gridsquare']) ?>"></div>
<div class="col-12"><label class="form-label">Note</label><textarea class="form-control" name="notes" rows="3"><?= e($q['notes']) ?></textarea></div>
<div class="col-12 d-flex gap-2"><button class="btn btn-primary">Salva</button><a class="btn btn-danger" href="delete_qso.php?id=<?= $id ?>">Elimina</a><a class="btn btn-secondary" href="dashboard_qso.php?log_id=<?= (int)$q['log_id'] ?>">Annulla</a></div>
</div></form><?php require_once __DIR__ . '/footer.php'; ?>
