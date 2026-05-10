<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$stmt = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'superadmin'");
$installed = (int)$stmt->fetch()['c'] > 0;
$error = '';

if ($installed) {
    http_response_code(403);
    die('Sistema già installato. Per sicurezza elimina install.php dal server.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($username === '' || strlen($username) < 3) {
        $error = 'Username troppo breve.';
    } elseif (strlen($password) < 8) {
        $error = 'La password deve avere almeno 8 caratteri.';
    } elseif ($password !== $confirm) {
        $error = 'Le password non coincidono.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, active) VALUES (?, ?, 'superadmin', 1)");
        $stmt->execute([$username, $hash]);
        header('Location: login.php?installed=1');
        exit;
    }
}
require_once __DIR__ . '/header.php';
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Installazione HamLog</h1>
                <p class="small-muted">Crea il primo utente superadmin.</p>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
                    <div class="mb-3"><label class="form-label">Conferma password</label><input type="password" class="form-control" name="confirm" required></div>
                    <button class="btn btn-primary w-100">Crea superadmin</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
