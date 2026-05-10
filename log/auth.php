<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged() {
    return !empty($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged()) {
        header('Location: login.php');
        exit;
    }
}

function is_superadmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin';
}

function require_superadmin() {
    require_login();
    if (!is_superadmin()) {
        http_response_code(403);
        die('Accesso negato');
    }
}

function current_user_id() {
    return (int)($_SESSION['user_id'] ?? 0);
}
