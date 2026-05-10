<?php require_once __DIR__ . '/functions.php'; ?>
<!doctype html>
<html lang="it" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #101114; }
        .navbar, .card { border-color: #2b2f36 !important; }
        .form-control, .form-select { background-color:#181b20; border-color:#343a40; color:#fff; }
        .form-control:focus, .form-select:focus { background-color:#181b20; color:#fff; }
        .qso-table-wrap { max-height: 45vh; overflow:auto; }
        .call-input { text-transform: uppercase; letter-spacing: .04em; }
        .small-muted { color:#9aa0a6; font-size:.9rem; }
        @media (max-width: 576px) {
            .btn-lg-mobile { padding:.75rem 1rem; font-size:1.1rem; }
            .qso-table-wrap { max-height: 42vh; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom mb-3">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard_logs.php"><?= e(APP_NAME) ?></a>
        <div class="d-flex gap-2 align-items-center">
            <?php if (is_logged()): ?>
                <?php if (is_superadmin()): ?><a class="btn btn-sm btn-outline-warning" href="admin_users.php">Admin</a><?php endif; ?>
                <a class="btn btn-sm btn-outline-light" href="dashboard_logs.php">I miei log</a>
                <a class="btn btn-sm btn-outline-danger" href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container-fluid pb-4">
