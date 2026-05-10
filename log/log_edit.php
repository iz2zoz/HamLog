<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_login();

$userId = current_user_id();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM logs WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
$log = $stmt->fetch();
if (!$log) { die('Log non trovato'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_name = trim($_POST['log_name'] ?? '');
    $station_call = normalize_call($_POST['station_call'] ?? '');
    $timezone = trim($_POST['timezone'] ?? 'Europe/Rome');
    $my_gridsquare = strtoupper(trim($_POST['my_gridsquare'] ?? ''));
    $reference = strtoupper(trim($_POST['reference'] ?? ''));

    if ($log_name === '' || $station_call === '') {
        $error = 'Nome log e nominativo usato sono obbligatori.';
    } else {
        try {
            new DateTimeZone($timezone);
            $stmt = $pdo->prepare('UPDATE logs SET log_name = ?, station_call = ?, timezone = ?, my_gridsquare = ?, reference = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$log_name, $station_call, $timezone, $my_gridsquare ?: null, $reference ?: null, $id, $userId]);
            header('Location: dashboard_logs.php');
            exit;
        } catch (Exception $e) {
            $error = 'Fuso orario non valido. Esempio: Europe/Rome';
        }
    }
}
require_once __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Modifica log</h1>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card">
  <div class="card-body row g-3">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="col-md-6"><label class="form-label">Nome log</label><input class="form-control" name="log_name" value="<?= e($log['log_name']) ?>" required autofocus></div>
    <div class="col-md-3"><label class="form-label">Nominativo usato</label><input class="form-control call-input" name="station_call" value="<?= e($log['station_call']) ?>" required></div>
    <div class="col-md-3"><label class="form-label">Fuso orario</label><input class="form-control" name="timezone" value="<?= e($log['timezone']) ?>" required></div>
    <div class="col-md-3"><label class="form-label">Locator</label><input class="form-control call-input" name="my_gridsquare" maxlength="6" value="<?= e($log['my_gridsquare']) ?>"></div>
    <div class="col-md-3"><label class="form-label">Referenza SOTA/POTA</label><input class="form-control call-input" name="reference" value="<?= e($log['reference']) ?>"></div>
    <div class="col-12"><button class="btn btn-primary">Salva modifiche</button> <a class="btn btn-secondary" href="dashboard_logs.php">Annulla</a></div>
  </div>
</form>
<?php require_once __DIR__ . '/footer.php'; ?>
