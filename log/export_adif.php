<?php
require_once __DIR__ . '/db.php'; require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; require_login();
$userId=current_user_id(); $logId=(int)($_GET['log_id']??0);
$stmt=$pdo->prepare('SELECT * FROM logs WHERE id=? AND user_id=?'); $stmt->execute([$logId,$userId]); $log=$stmt->fetch(); if(!$log){die('Log non trovato');}
$stmt=$pdo->prepare('SELECT * FROM qso WHERE log_id=? AND user_id=? ORDER BY qso_datetime_utc ASC'); $stmt->execute([$logId,$userId]); $qsos=$stmt->fetchAll();
$filename=preg_replace('/[^A-Za-z0-9_\-]/','_', $log['log_name']).'.adi';
header('Content-Type: text/plain; charset=utf-8'); header('Content-Disposition: attachment; filename="'.$filename.'"');
echo adif_field('ADIF_VER','3.1.4') . "\n"; echo adif_field('PROGRAMID','HamLog') . "\n"; echo "<EOH>\n\n";
foreach($qsos as $q){ $dt=new DateTime($q['qso_datetime_utc'], new DateTimeZone('UTC'));
    $line=''; $line.=adif_field('CALL',$q['callsign']); $line.=adif_field('QSO_DATE',$dt->format('Ymd')); $line.=adif_field('TIME_ON',$dt->format('His'));
    $line.=adif_field('BAND',$q['band']); $line.=adif_field('MODE',$q['mode']); $line.=adif_field('RST_SENT',$q['rst_sent']); $line.=adif_field('RST_RCVD',$q['rst_rcvd']);
    $line.=adif_field('FREQ',$q['freq']); $line.=adif_field('STATION_CALLSIGN',$log['station_call']); $line.=adif_field('MY_GRIDSQUARE',$log['my_gridsquare']);
    $line.=adif_field('NAME',$q['name']); $line.=adif_field('QTH',$q['qth']); $line.=adif_field('GRIDSQUARE',$q['gridsquare']);
    if(!empty($log['reference'])) $line.=adif_field('COMMENT',$log['reference']); if(!empty($q['notes'])) $line.=adif_field('NOTES',$q['notes']);
    echo trim($line).' <EOR>' . "\n";
}
exit;
