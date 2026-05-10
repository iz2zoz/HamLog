<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_login();

function wants_json() {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return stripos($accept, 'application/json') !== false || strtolower($xhr) === 'fetch';
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

$isJson = wants_json();
$userId = current_user_id();
$logId = (int)($_POST['log_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM logs WHERE id = ? AND user_id = ?');
$stmt->execute([$logId, $userId]);
$log = $stmt->fetch();

if (!$log) {
    if ($isJson) json_response(['success' => false, 'error' => 'Log non trovato'], 404);
    die('Log non trovato');
}

$callsign = normalize_call($_POST['callsign'] ?? '');
if ($callsign === '') {
    if ($isJson) json_response(['success' => false, 'error' => 'Call obbligatorio'], 422);
    header('Location: dashboard_qso.php?log_id=' . $logId);
    exit;
}

$band = valid_choice($_POST['band'] ?? '20m', bands(), '20m');
$mode = valid_choice($_POST['mode'] ?? 'SSB', modes(), 'SSB');
$rst_sent = trim($_POST['rst_sent'] ?? default_rst_for_mode($mode));
$rst_rcvd = trim($_POST['rst_rcvd'] ?? default_rst_for_mode($mode));
$freq = trim($_POST['freq'] ?? '');
$freqVal = $freq === '' ? null : str_replace(',', '.', $freq);
$name = trim($_POST['name'] ?? '');
$qth = trim($_POST['qth'] ?? '');
$gridsquare = strtoupper(trim($_POST['gridsquare'] ?? ''));
$notes = trim($_POST['notes'] ?? '');

$tz = new DateTimeZone($log['timezone'] ?: 'UTC');
if (!empty($_POST['auto_dt'])) {
    $local = new DateTime('now', $tz);
} else {
    $d = $_POST['local_date'] ?? date('Y-m-d');
    $t = $_POST['local_time'] ?? date('H:i:s');
    $local = new DateTime($d . ' ' . $t, $tz);
}
$utc = clone $local;
$utc->setTimezone(new DateTimeZone('UTC'));

$stmt = $pdo->prepare('INSERT INTO qso (user_id, log_id, callsign, band, mode, freq, rst_sent, rst_rcvd, name, qth, gridsquare, notes, qso_datetime_utc, qso_datetime_local) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $userId,
    $logId,
    $callsign,
    $band,
    $mode,
    $freqVal,
    $rst_sent,
    $rst_rcvd,
    $name !== '' ? $name : null,
    $qth !== '' ? $qth : null,
    $gridsquare !== '' ? $gridsquare : null,
    $notes !== '' ? $notes : null,
    $utc->format('Y-m-d H:i:s'),
    $local->format('Y-m-d H:i:s')
]);

$qsoId = (int)$pdo->lastInsertId();
$totalStmt = $pdo->prepare('SELECT COUNT(*) AS c FROM qso WHERE log_id = ? AND user_id = ?');
$totalStmt->execute([$logId, $userId]);
$total = (int)$totalStmt->fetch()['c'];

if ($isJson) {
    json_response([
        'success' => true,
        'qso' => [
            'id' => $qsoId,
            'qso_datetime_local' => $local->format('Y-m-d H:i:s'),
            'callsign' => $callsign,
            'band' => $band,
            'mode' => $mode,
            'freq' => $freqVal,
            'rst_sent' => $rst_sent,
            'rst_rcvd' => $rst_rcvd,
            'name' => $name,
            'qth' => $qth,
            'gridsquare' => $gridsquare,
            'notes' => $notes
        ],
        'total' => $total
    ]);
}

header('Location: dashboard_qso.php?log_id=' . $logId);
exit;
