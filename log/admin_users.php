<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_superadmin();
$stmt=$pdo->query('SELECT u.*, COUNT(DISTINCT l.id) AS log_count, COUNT(q.id) AS qso_count FROM users u LEFT JOIN logs l ON l.user_id=u.id LEFT JOIN qso q ON q.user_id=u.id GROUP BY u.id ORDER BY u.created_at DESC'); $users=$stmt->fetchAll();
require_once __DIR__ . '/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3">Utenti</h1><a class="btn btn-primary" href="admin_user_new.php">Nuovo utente</a></div>
<div class="card"><div class="table-responsive"><table class="table table-dark table-striped mb-0 align-middle"><thead><tr><th>Username</th><th>Ruolo</th><th>Stato</th><th>Log</th><th>QSO</th><th></th></tr></thead><tbody>
<?php foreach($users as $u): ?><tr><td><?= e($u['username']) ?></td><td><?= e($u['role']) ?></td><td><?= (int)$u['active']?'attivo':'disattivato' ?></td><td><?= (int)$u['log_count'] ?></td><td><?= (int)$u['qso_count'] ?></td><td><a class="btn btn-sm btn-warning" href="admin_user_edit.php?id=<?= (int)$u['id'] ?>">modifica</a></td></tr><?php endforeach; ?>
</tbody></table></div></div><?php require_once __DIR__ . '/footer.php'; ?>
