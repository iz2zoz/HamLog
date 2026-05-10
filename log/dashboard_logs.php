<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_login();
$userId = current_user_id();
$stmt = $pdo->prepare('SELECT l.*, COUNT(q.id) AS qso_count FROM logs l LEFT JOIN qso q ON q.log_id = l.id AND q.user_id = l.user_id WHERE l.user_id = ? GROUP BY l.id ORDER BY l.created_at DESC');
$stmt->execute([$userId]);
$logs = $stmt->fetchAll();
require_once __DIR__ . '/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">I miei log</h1>
    <a class="btn btn-primary" href="log_new.php">Nuovo log</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-striped mb-0 align-middle">
                <thead><tr><th>Nome</th><th>Call</th><th>Ref</th><th>QSO</th><th>Azioni</th></tr></thead>
                <tbody>
                <?php if (!$logs): ?><tr><td colspan="5" class="text-center py-4">Nessun log. Creane uno per iniziare.</td></tr><?php endif; ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= e($log['log_name']) ?></td>
                        <td><?= e($log['station_call']) ?></td>
                        <td><?= e($log['reference'] ?: '-') ?></td>
                        <td><?= (int)$log['qso_count'] ?></td>
                        <td class="d-flex flex-wrap gap-1">
                            <a class="btn btn-sm btn-success" href="dashboard_qso.php?log_id=<?= (int)$log['id'] ?>">Apri</a>
                            <a class="btn btn-sm btn-outline-warning" href="log_edit.php?id=<?= (int)$log['id'] ?>">Modifica</a>
                            <a class="btn btn-sm btn-outline-info" href="export_adif.php?log_id=<?= (int)$log['id'] ?>">ADIF</a>
                            <a class="btn btn-sm btn-outline-danger" href="log_delete.php?id=<?= (int)$log['id'] ?>">Elimina</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
