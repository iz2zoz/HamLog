<?php
require_once __DIR__ . '/auth.php';
header('Location: ' . (is_logged() ? 'dashboard_logs.php' : 'login.php'));
exit;
