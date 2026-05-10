<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_login();

$userId = current_user_id();
$logId = (int)($_GET['log_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM logs WHERE id = ? AND user_id = ?');
$stmt->execute([$logId, $userId]);
$log = $stmt->fetch();
if (!$log) { die('Log non trovato'); }

$countStmt = $pdo->prepare('SELECT COUNT(*) AS c FROM qso WHERE log_id = ? AND user_id = ?');
$countStmt->execute([$logId, $userId]);
$qsoTotal = (int)$countStmt->fetch()['c'];

$stmt = $pdo->prepare('SELECT * FROM qso WHERE log_id = ? AND user_id = ? ORDER BY qso_datetime_utc DESC LIMIT 500');
$stmt->execute([$logId, $userId]);
$qsos = $stmt->fetchAll();
require_once __DIR__ . '/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h1 class="h4 mb-0"><?= e($log['log_name']) ?> <span class="small-muted">(<?= e($log['station_call']) ?>)</span></h1>
    <div class="small-muted"><?= e($log['reference'] ?: '') ?></div>
    <div class="mt-2">
      <span class="badge text-bg-success fs-6">Totale QSO: <span id="qsoTotal"><?= $qsoTotal ?></span></span>
      <span id="pendingBadge" class="badge text-bg-warning fs-6 d-none">Non sincronizzati: <span id="pendingCount">0</span></span>
      <button id="syncBtn" class="btn btn-sm btn-warning d-none">Sincronizza ora</button>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-info" href="export_adif.php?log_id=<?= $logId ?>">Export ADIF</a>
    <a class="btn btn-outline-light" href="dashboard_logs.php">Torna ai log</a>
  </div>
</div>

<div id="syncStatus" class="alert alert-warning d-none"></div>

<div class="card mb-3"><div class="card-body">
<form id="qsoForm" method="post" action="save_qso.php" class="row g-2 align-items-end">
<input type="hidden" name="log_id" value="<?= $logId ?>">
<input type="hidden" name="band" id="band_hidden"><input type="hidden" name="mode" id="mode_hidden">
<div class="col-6 col-md-2"><label class="form-label">Banda</label><select id="band" class="form-select"><?php foreach(bands() as $b): ?><option><?= e($b) ?></option><?php endforeach; ?></select></div>
<div class="col-6 col-md-2"><label class="form-label">Modo</label><select id="mode" class="form-select"><?php foreach(modes() as $m): ?><option><?= e($m) ?></option><?php endforeach; ?></select></div>
<div class="col-8 col-md-4"><label class="form-label">CALL corrispondente</label><input id="callsign" name="callsign" class="form-control form-control-lg call-input" required autofocus autocomplete="off"></div>
<div class="col-4 col-md-2"><button class="btn btn-primary btn-lg w-100 btn-lg-mobile">LOG</button></div>
<div class="col-6 col-md-1"><label class="form-label">RST S</label><input id="rst_sent" name="rst_sent" class="form-control" value="59"></div>
<div class="col-6 col-md-1"><label class="form-label">RST R</label><input id="rst_rcvd" name="rst_rcvd" class="form-control" value="59"></div>
<div class="col-12 col-md-2"><label class="form-label">Freq opzionale</label><input name="freq" class="form-control" inputmode="decimal" placeholder="14.250"></div>
<div class="col-12 col-md-3"><label class="form-label">Nome</label><input name="name" class="form-control" autocomplete="off"></div>
<div class="col-12 col-md-3"><label class="form-label">QTH</label><input name="qth" class="form-control" autocomplete="off"></div>
<div class="col-12 col-md-3"><label class="form-label">Locator corr.</label><input name="gridsquare" class="form-control call-input" maxlength="10" autocomplete="off"></div>
<div class="col-12"><label class="form-label">Note</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
<div class="col-12"><div class="form-check mt-1"><input class="form-check-input" type="checkbox" id="auto_dt" name="auto_dt" value="1" checked><label class="form-check-label" for="auto_dt">Data/ora automatiche</label></div></div>
<div class="col-6 col-md-3 manual-dt d-none"><label class="form-label">Data locale</label><input type="date" name="local_date" class="form-control"></div>
<div class="col-6 col-md-3 manual-dt d-none"><label class="form-label">Ora locale</label><input type="time" name="local_time" class="form-control" step="1"></div>
</form>
</div></div>

<div class="card"><div class="card-header">Ultimi QSO</div><div class="qso-table-wrap table-responsive"><table class="table table-dark table-striped table-sm mb-0 align-middle"><thead><tr><th>Ora locale</th><th>Call</th><th>Banda</th><th>Modo</th><th>RST</th><th>Freq</th><th>Nome/QTH</th><th></th></tr></thead><tbody id="qsoTbody">
<?php if(!$qsos): ?><tr id="emptyRow"><td colspan="8" class="text-center py-4">Nessun QSO registrato.</td></tr><?php endif; ?>
<?php foreach($qsos as $q): ?><tr data-qso-id="<?= (int)$q['id'] ?>"><td><?= e(substr($q['qso_datetime_local'],0,16)) ?></td><td class="fw-bold"><?= e($q['callsign']) ?></td><td><?= e($q['band']) ?></td><td><?= e($q['mode']) ?></td><td><?= e($q['rst_sent'].'/'.$q['rst_rcvd']) ?></td><td><?= e($q['freq'] ?: '-') ?></td><td><?= e(trim(($q['name'] ?: '').' '.($q['qth'] ? '(' . $q['qth'] . ')' : '')) ?: '-') ?></td><td><a class="btn btn-sm btn-warning" href="edit_qso.php?id=<?= (int)$q['id'] ?>">mod</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>

<script>
const logId = <?= (int)$logId ?>;
const logKey = 'hamlog_' + logId + '_';
const pendingKey = logKey + 'pending_qso';
const form = document.getElementById('qsoForm');
const band = document.getElementById('band');
const mode = document.getElementById('mode');
const rstS = document.getElementById('rst_sent');
const rstR = document.getElementById('rst_rcvd');
const tbody = document.getElementById('qsoTbody');
const qsoTotalEl = document.getElementById('qsoTotal');
const pendingBadge = document.getElementById('pendingBadge');
const pendingCountEl = document.getElementById('pendingCount');
const syncBtn = document.getElementById('syncBtn');
const syncStatus = document.getElementById('syncStatus');

band.value = localStorage.getItem(logKey+'band') || '20m';
mode.value = localStorage.getItem(logKey+'mode') || 'SSB';

function applyDefaults(force=true){
  localStorage.setItem(logKey+'band', band.value);
  localStorage.setItem(logKey+'mode', mode.value);
  if(force){ const rst = mode.value === 'CW' ? '599' : '59'; rstS.value = rst; rstR.value = rst; }
}
mode.addEventListener('change', () => applyDefaults(true));
band.addEventListener('change', () => applyDefaults(false));
applyDefaults(false);

document.getElementById('auto_dt').addEventListener('change', function(){
  document.querySelectorAll('.manual-dt').forEach(el => el.classList.toggle('d-none', this.checked));
});

document.getElementById('callsign').addEventListener('keydown', function(e){
  if(e.key==='Enter'){
    e.preventDefault();
    form.requestSubmit();
  }
});

function esc(s){
  return String(s ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
}
function getPending(){
  try { return JSON.parse(localStorage.getItem(pendingKey) || '[]'); } catch(e) { return []; }
}
function setPending(items){
  localStorage.setItem(pendingKey, JSON.stringify(items));
  updatePendingUI();
}
function updatePendingUI(){
  const count = getPending().length;
  pendingCountEl.textContent = count;
  pendingBadge.classList.toggle('d-none', count === 0);
  syncBtn.classList.toggle('d-none', count === 0);
}
function buildPayload(){
  document.getElementById('band_hidden').value = band.value;
  document.getElementById('mode_hidden').value = mode.value;
  const fd = new FormData(form);
  if (!document.getElementById('auto_dt').checked) fd.delete('auto_dt');
  const obj = {};
  fd.forEach((v,k)=> obj[k]=v);
  obj.local_client_id = 'local_' + Date.now() + '_' + Math.random().toString(36).slice(2);
  return obj;
}
function payloadToFormData(payload){
  const fd = new FormData();
  Object.entries(payload).forEach(([k,v]) => { if(v !== undefined && v !== null) fd.append(k, v); });
  return fd;
}
function localTimeForDisplay(payload){
  if (payload.auto_dt === '1') {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    const h = String(d.getHours()).padStart(2,'0');
    const min = String(d.getMinutes()).padStart(2,'0');
    return `${y}-${m}-${day} ${h}:${min}`;
  }
  return `${payload.local_date || ''} ${(payload.local_time || '').slice(0,5)}`.trim();
}
function addRow(qso, pending=false, localId=null){
  const empty = document.getElementById('emptyRow');
  if (empty) empty.remove();
  const tr = document.createElement('tr');
  if (pending) {
    tr.className = 'table-warning';
    tr.dataset.pendingId = localId || qso.local_client_id || '';
  } else if (qso.id) {
    tr.dataset.qsoId = qso.id;
  }
  const localTime = qso.qso_datetime_local ? qso.qso_datetime_local.slice(0,16) : localTimeForDisplay(qso);
  const freq = qso.freq ? qso.freq : '-';
  const rst = `${qso.rst_sent || ''}/${qso.rst_rcvd || ''}`;
  const nameQth = `${qso.name || ''} ${qso.qth ? '(' + qso.qth + ')' : ''}`.trim() || '-';
  tr.innerHTML = `<td>${esc(localTime)}</td><td class="fw-bold">${esc((qso.callsign || '').toUpperCase())} ${pending ? '<span class="badge text-bg-warning text-dark ms-1">NON SYNC</span>' : ''}</td><td>${esc(qso.band)}</td><td>${esc(qso.mode)}</td><td>${esc(rst)}</td><td>${esc(freq)}</td><td>${esc(nameQth)}</td><td>${pending ? '<span class="small text-dark">in coda</span>' : `<a class="btn btn-sm btn-warning" href="edit_qso.php?id=${encodeURIComponent(qso.id)}">mod</a>`}</td>`;
  tbody.prepend(tr);
}
function removePendingRow(localId){
  const row = tbody.querySelector(`[data-pending-id="${CSS.escape(localId)}"]`);
  if (row) row.remove();
}
async function sendPayload(payload){
  const res = await fetch('save_qso.php', {
    method: 'POST',
    body: payloadToFormData(payload),
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' }
  });
  if (!res.ok) throw new Error('HTTP ' + res.status);
  const json = await res.json();
  if (!json.success) throw new Error(json.error || 'Salvataggio non riuscito');
  return json;
}
function queuePayload(payload){
  const pending = getPending();
  pending.push(payload);
  setPending(pending);
  addRow(payload, true, payload.local_client_id);
}
function resetFastFields(){
  form.callsign.value = '';
  form.name.value = '';
  form.qth.value = '';
  form.gridsquare.value = '';
  form.notes.value = '';
  form.freq.value = '';
  form.callsign.focus();
}
form.addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = buildPayload();
  payload.callsign = (payload.callsign || '').trim().toUpperCase();
  payload.gridsquare = (payload.gridsquare || '').trim().toUpperCase();
  if (!payload.callsign) return;
  try {
    const result = await sendPayload(payload);
    addRow(result.qso, false);
    qsoTotalEl.textContent = result.total;
    resetFastFields();
    syncPending();
  } catch(err) {
    queuePayload(payload);
    resetFastFields();
    syncStatus.textContent = 'Connessione/server non disponibile: QSO salvato localmente e non ancora sincronizzato.';
    syncStatus.classList.remove('d-none');
  }
});
async function syncPending(){
  let pending = getPending();
  if (pending.length === 0) { updatePendingUI(); return; }
  syncStatus.classList.add('d-none');
  const stillPending = [];
  for (const payload of pending) {
    try {
      const result = await sendPayload(payload);
      removePendingRow(payload.local_client_id);
      addRow(result.qso, false);
      qsoTotalEl.textContent = result.total;
    } catch(err) {
      stillPending.push(payload);
    }
  }
  setPending(stillPending);
  if (stillPending.length > 0) {
    syncStatus.textContent = stillPending.length + ' QSO ancora non sincronizzati.';
    syncStatus.classList.remove('d-none');
  }
}
syncBtn.addEventListener('click', syncPending);
window.addEventListener('online', syncPending);

// Ripristina visivamente eventuali QSO rimasti in coda da una sessione precedente.
getPending().forEach(p => addRow(p, true, p.local_client_id));
updatePendingUI();
syncPending();
</script>
<?php require_once __DIR__ . '/footer.php'; ?>
