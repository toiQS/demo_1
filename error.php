<?php
/**
 * error.php — Trang xem nhật ký lỗi hệ thống
 * Hiển thị tất cả log lỗi từ PHP, DB, và các module
 */

// ── Xử lý xóa log ───────────────────────────────────────────
if (isset($_POST['clear']) && !empty($_POST['file'])) {
    $target = realpath($_POST['file']);
    $base   = realpath(__DIR__ . '/logs');
    if ($target && $base && strpos($target, $base) === 0 && is_writable($target)) {
        file_put_contents($target, '');
        header('Location: error.php?cleared=1');
        exit;
    }
}

// ── Thu thập log files ────────────────────────────────────────
$logSources = [
    'DB Connect'        => 'logs/dbconnect.txt',
    'Dashboard/Stats'   => 'logs/index/dashboard.text',
    'Category – Add'    => 'logs/category/add.txt',
    'Category – Gets'   => 'logs/category/gets.txt',
];

$allEntries = [];   // ['source', 'time', 'level', 'message', 'file']
$fileSizes  = [];

foreach ($logSources as $label => $path) {
    $fullPath = __DIR__ . '/' . $path;
    $fileSizes[$path] = file_exists($fullPath) ? filesize($fullPath) : 0;
    if (!file_exists($fullPath) || filesize($fullPath) === 0) continue;

    $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($lines) as $line) {
        // Phân tích dạng: [2026-01-01 12:00:00] [TYPE/code] message in file on line N
        // hoặc:           [2026-01-01 12:00:00] [MODULE] message
        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(.+)$/', $line, $m);
        if (!$m) continue;

        $timestamp = $m[1];
        $rest      = $m[2];

        // Phân loại level
        $level = 'info';
        if (preg_match('/\[(\d+)\]/', $rest, $lm)) {
            $code = (int) $lm[1];
            $level = match(true) {
                $code === 1 || $code === 64  => 'fatal',
                $code === 2 || $code === 512 => 'warning',
                $code === 8 || $code === 256 => 'notice',
                default                      => 'info',
            };
            $rest = ltrim(substr($rest, strlen($lm[0])));
        } elseif (stripos($rest, 'error') !== false || stripos($rest, 'lỗi') !== false) {
            $level = 'warning';
        }

        // Tách message và file location
        $fileLoc = '';
        if (preg_match('/\s+in\s+(.+?)\s+on\s+line\s+(\d+)$/', $rest, $fm)) {
            $fileLoc = $fm[1] . ':' . $fm[2];
            $rest    = substr($rest, 0, -strlen($fm[0]));
        }

        $allEntries[] = [
            'source'  => $label,
            'srcFile' => $path,
            'srcFull' => $fullPath,
            'time'    => $timestamp,
            'level'   => $level,
            'message' => trim($rest),
            'fileLoc' => $fileLoc,
        ];
    }
}

// Sắp xếp theo thời gian mới nhất
usort($allEntries, fn($a, $b) => strcmp($b['time'], $a['time']));

$totalErrors   = count(array_filter($allEntries, fn($e) => in_array($e['level'], ['fatal','warning'])));
$totalWarnings = count(array_filter($allEntries, fn($e) => $e['level'] === 'warning'));
$totalAll      = count($allEntries);

// ── Layout ────────────────────────────────────────────────────
$page_title    = 'Error Log';
$page_subtitle  = 'Nhật ký lỗi & cảnh báo hệ thống';
$active_nav    = '';
require_once 'includes/layout.php';
?>

<style>
/* ── Error page styles ──────────────────────────────────────── */
.err-stats  { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
.err-box    { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius);
              padding:16px 18px; display:flex; align-items:center; gap:14px; }
.err-box i  { width:40px; height:40px; border-radius:9px; display:flex; align-items:center;
              justify-content:center; font-size:18px; flex-shrink:0; }
.e-red   i  { background:rgba(248,81,73,.12);   color:var(--red); }
.e-orange i { background:rgba(240,165,0,.12);   color:var(--accent); }
.e-blue  i  { background:rgba(88,166,255,.12);  color:var(--blue); }
.e-green i  { background:rgba(63,185,80,.12);   color:var(--green); }
.err-val    { font-size:22px; font-weight:800; font-family:var(--mono); line-height:1; }
.err-lbl    { font-size:11px; color:var(--text-muted); margin-top:3px; font-weight:600;
              text-transform:uppercase; letter-spacing:.04em; }

.toolbar    { display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.search-wrap{ position:relative; flex:1; min-width:180px; max-width:300px; }
.search-wrap i { position:absolute; left:11px; top:50%; transform:translateY(-50%);
                 color:var(--text-muted); font-size:13px; pointer-events:none; }
.search-wrap .form-control { padding-left:34px; }

/* Level badges */
.lvl-badge  { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700;
              padding:2px 9px; border-radius:20px; white-space:nowrap; }
.lvl-fatal  { background:rgba(248,81,73,.18);  color:var(--red); }
.lvl-warning{ background:rgba(240,165,0,.18);  color:var(--accent); }
.lvl-notice { background:rgba(88,166,255,.18); color:var(--blue); }
.lvl-info   { background:rgba(63,185,80,.12);  color:var(--green); }

/* Log table */
.log-table  { width:100%; border-collapse:collapse; font-size:13px; }
.log-table thead th { font-size:11px; font-weight:600; text-transform:uppercase;
                      letter-spacing:.6px; color:var(--text-muted); padding:10px 16px;
                      text-align:left; border-bottom:1px solid var(--border); white-space:nowrap; }
.log-table tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
.log-table tbody tr:last-child { border-bottom:none; }
.log-table tbody tr:hover { background:rgba(255,255,255,.025); }
.log-table tbody td { padding:11px 16px; vertical-align:top; }

.log-time   { font-family:var(--mono); font-size:11px; color:var(--text-muted); white-space:nowrap; }
.log-source { font-size:11px; font-weight:700; color:var(--accent); }
.log-msg    { font-size:12.5px; line-height:1.5; word-break:break-all; }
.log-file   { font-size:11px; color:var(--text-muted); font-family:var(--mono);
              margin-top:3px; word-break:break-all; }

/* Files panel */
.files-panel { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:12px; margin-bottom:20px; }
.file-card  { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius);
              padding:16px; display:flex; flex-direction:column; gap:8px; }
.file-name  { font-size:12px; font-weight:700; color:var(--text-primary); word-break:break-all; }
.file-size  { font-size:11px; color:var(--text-muted); font-family:var(--mono); }
.file-empty { color:var(--green); }

.btn-clear  { background:rgba(248,81,73,.1); color:var(--red);
              border:1px solid rgba(248,81,73,.25); padding:5px 12px;
              border-radius:var(--radius-sm); font-size:12px; font-weight:600;
              cursor:pointer; display:flex; align-items:center; gap:5px; transition:.2s; }
.btn-clear:hover { background:rgba(248,81,73,.2); }

.empty-state { text-align:center; padding:56px 0; color:var(--text-muted); }
.empty-state i { font-size:40px; display:block; margin-bottom:12px; opacity:.25; }

@media(max-width:900px){ .err-stats{grid-template-columns:repeat(2,1fr);} }
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
.err-box,.file-card { animation:fadeUp .35s ease both; }
</style>

<?php if (isset($_GET['cleared'])): ?>
<div style="background:rgba(63,185,80,.12);border:1px solid rgba(63,185,80,.25);color:var(--green);
            border-radius:var(--radius);padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;
            display:flex;align-items:center;gap:8px;animation:fadeUp .3s ease">
  <i class="fa-solid fa-circle-check"></i> Đã xóa log thành công.
</div>
<?php endif; ?>

<!-- ── STATS ─────────────────────────────────────────────────── -->
<div class="err-stats">
  <div class="err-box e-red">
    <i class="fa-solid fa-circle-xmark"></i>
    <div><div class="err-val"><?= $totalErrors ?></div><div class="err-lbl">Lỗi nghiêm trọng</div></div>
  </div>
  <div class="err-box e-orange">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <div><div class="err-val"><?= $totalWarnings ?></div><div class="err-lbl">Cảnh báo</div></div>
  </div>
  <div class="err-box e-blue">
    <i class="fa-solid fa-list"></i>
    <div><div class="err-val"><?= $totalAll ?></div><div class="err-lbl">Tổng mục log</div></div>
  </div>
  <div class="err-box e-green">
    <i class="fa-solid fa-file-lines"></i>
    <div><div class="err-val"><?= count($logSources) ?></div><div class="err-lbl">File log</div></div>
  </div>
</div>

<!-- ── LOG FILES STATUS ───────────────────────────────────────── -->
<div class="section-header" style="margin-bottom:12px">
  <div class="section-title">Trạng thái file log</div>
</div>
<div class="files-panel">
  <?php foreach ($logSources as $label => $path):
    $sz = $fileSizes[$path] ?? 0;
    $empty = $sz === 0;
  ?>
  <div class="file-card">
    <div class="file-name"><i class="fa-solid fa-file-lines" style="color:var(--text-muted);margin-right:6px"></i><?= htmlspecialchars($label) ?></div>
    <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($path) ?></div>
    <div class="file-size <?= $empty ? 'file-empty' : '' ?>">
      <?= $empty ? '✓ Trống' : number_format($sz) . ' bytes' ?>
    </div>
    <?php if (!$empty): ?>
    <form method="POST" onsubmit="return confirm('Xóa toàn bộ log của \'<?= addslashes($label) ?>\'?')">
      <input type="hidden" name="file" value="<?= htmlspecialchars(__DIR__ . '/' . $path) ?>">
      <button type="submit" name="clear" value="1" class="btn-clear">
        <i class="fa-solid fa-trash-can"></i> Xóa log
      </button>
    </form>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<!-- ── TOOLBAR ────────────────────────────────────────────────── -->
<div class="section-header" style="margin-bottom:12px">
  <div class="section-title">Nhật ký lỗi</div>
  <div style="display:flex;gap:8px;align-items:center">
    <button class="btn btn-secondary btn-sm" onclick="location.reload()" title="Làm mới">
      <i class="fa-solid fa-rotate-right"></i> Làm mới
    </button>
  </div>
</div>

<div class="toolbar">
  <div class="search-wrap">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="searchLog" class="form-control" placeholder="Tìm trong log..." oninput="filterLog()">
  </div>
  <select id="filterLevel" class="form-control" style="width:150px" onchange="filterLog()">
    <option value="">Tất cả mức độ</option>
    <option value="fatal">Fatal</option>
    <option value="warning">Warning</option>
    <option value="notice">Notice</option>
    <option value="info">Info</option>
  </select>
  <select id="filterSource" class="form-control" style="width:180px" onchange="filterLog()">
    <option value="">Tất cả nguồn</option>
    <?php foreach (array_keys($logSources) as $lbl): ?>
    <option value="<?= htmlspecialchars($lbl) ?>"><?= htmlspecialchars($lbl) ?></option>
    <?php endforeach; ?>
  </select>
  <span id="countBadge" style="font-size:12px;color:var(--text-muted);margin-left:auto">
    <?= $totalAll ?> mục
  </span>
</div>

<!-- ── LOG TABLE ─────────────────────────────────────────────── -->
<div class="card" style="overflow:hidden">
  <div style="overflow-x:auto">
    <?php if (empty($allEntries)): ?>
    <div class="empty-state">
      <i class="fa-solid fa-circle-check"></i>
      Không có lỗi nào được ghi nhận. Hệ thống đang hoạt động bình thường!
    </div>
    <?php else: ?>
    <table class="log-table" id="logTable">
      <thead>
        <tr>
          <th style="width:145px">Thời gian</th>
          <th style="width:130px">Mức độ</th>
          <th style="width:130px">Nguồn</th>
          <th>Thông báo lỗi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allEntries as $e): ?>
        <tr data-level="<?= $e['level'] ?>" data-source="<?= htmlspecialchars($e['source']) ?>">
          <td><span class="log-time"><?= htmlspecialchars($e['time']) ?></span></td>
          <td>
            <?php $icons = ['fatal'=>'fa-circle-xmark','warning'=>'fa-triangle-exclamation','notice'=>'fa-circle-info','info'=>'fa-circle-check']; ?>
            <span class="lvl-badge lvl-<?= $e['level'] ?>">
              <i class="fa-solid <?= $icons[$e['level']] ?? 'fa-circle' ?>"></i>
              <?= strtoupper($e['level']) ?>
            </span>
          </td>
          <td><span class="log-source"><?= htmlspecialchars($e['source']) ?></span></td>
          <td>
            <div class="log-msg"><?= htmlspecialchars($e['message']) ?></div>
            <?php if ($e['fileLoc']): ?>
            <div class="log-file"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($e['fileLoc']) ?></div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<script>
function filterLog() {
  const kw  = document.getElementById('searchLog').value.toLowerCase();
  const lvl = document.getElementById('filterLevel').value;
  const src = document.getElementById('filterSource').value;
  let cnt   = 0;

  document.querySelectorAll('#logTable tbody tr').forEach(row => {
    const matchKw  = !kw  || row.textContent.toLowerCase().includes(kw);
    const matchLvl = !lvl || row.dataset.level === lvl;
    const matchSrc = !src || row.dataset.source === src;
    const show     = matchKw && matchLvl && matchSrc;
    row.style.display = show ? '' : 'none';
    if (show) cnt++;
  });

  const badge = document.getElementById('countBadge');
  if (badge) badge.textContent = cnt + ' mục';
}
</script>

<?php require_once 'includes/layout_footer.php'; ?>
