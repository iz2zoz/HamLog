<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

if (is_logged()) { header('Location: dashboard_logs.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && (int)$user['active'] === 1 && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard_logs.php');
        exit;
    }
    $error = 'Credenziali non valide o utente disattivato.';
}
require_once __DIR__ . '/header.php';
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Login</h1>
                <?php if (isset($_GET['installed'])): ?><div class="alert alert-success">Superadmin creato. Ora puoi accedere.</div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post">
                    <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required autofocus></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
                    <button class="btn btn-primary w-100">Entra</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
