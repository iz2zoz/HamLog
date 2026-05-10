<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_login();
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $log_name=trim($_POST['log_name']??''); $station_call=normalize_call($_POST['station_call']??'');
    $timezone=trim($_POST['timezone']??'Europe/Rome'); $grid=strtoupper(trim($_POST['my_gridsquare']??'')); $ref=strtoupper(trim($_POST['reference']??''));
    if ($log_name==='' || $station_call==='') $error='Nome log e nominativo usato sono obbligatori.';
    elseif ($grid!=='' && !preg_match('/^[A-R]{2}[0-9]{2}[A-X]{2}$/i',$grid)) $error='Locator non valido: usa 6 caratteri, es. JN45NK.';
    else {
        $stmt=$pdo->prepare('INSERT INTO logs (user_id, log_name, station_call, timezone, my_gridsquare, reference) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([current_user_id(),$log_name,$station_call,$timezone,$grid ?: null,$ref ?: null]);
        header('Location: dashboard_logs.php'); exit;
    }
}
require_once __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Nuovo log</h1>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card"><div class="card-body row g-3">
    <div class="col-md-6"><label class="form-label">Nome log</label><input class="form-control" name="log_name" placeholder="POTA IT-1234 / CQWW / Attivazione..." required autofocus></div>
    <div class="col-md-3"><label class="form-label">Nominativo usato</label><input class="form-control call-input" name="station_call" placeholder="IZ2ZOZ/P" required></div>
    <div class="col-md-3"><label class="form-label">Fuso orario</label><input class="form-control" name="timezone" value="Europe/Rome" required></div>
    <div class="col-md-3"><label class="form-label">Locator</label><input class="form-control call-input" name="my_gridsquare" maxlength="6" placeholder="JN45NK"></div>
    <div class="col-md-3"><label class="form-label">Referenza SOTA/POTA</label><input class="form-control call-input" name="reference" placeholder="IT-1234"></div>
    <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Crea log</button><a class="btn btn-secondary" href="dashboard_logs.php">Annulla</a></div>
</div></form>
<?php require_once __DIR__ . '/footer.php'; ?>
