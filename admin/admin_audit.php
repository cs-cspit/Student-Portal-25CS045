<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$db = app_mysqli();

$actionFilter = trim((string)($_GET['action_type'] ?? ''));
$actorFilter = trim((string)($_GET['actor'] ?? ''));
$search = trim((string)($_GET['search'] ?? ''));

$sql = "SELECT audit_id, actor_email, actor_role, action, details, ip_address, created_at 
        FROM audit_logs 
        WHERE 1=1";

$params = [];
$types = '';

if ($actionFilter !== '') {
    $sql .= " AND action = ?";
    $params[] = $actionFilter;
    $types .= 's';
}
if ($actorFilter !== '') {
    $sql .= " AND actor_email = ?";
    $params[] = $actorFilter;
    $types .= 's';
}
if ($search !== '') {
    $sql .= " AND (details LIKE ? OR actor_email LIKE ? OR action LIKE ? OR ip_address LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'ssss';
}

$sql .= " ORDER BY created_at DESC, audit_id DESC";

if (isset($_GET['export'])) {
    $stmt = $db->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="system-audit-log.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Log ID', 'Actor Email', 'Role', 'Action Code', 'Details', 'IP Address', 'Timestamp']);
    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [
            $row['audit_id'],
            $row['actor_email'],
            $row['actor_role'],
            $row['action'],
            $row['details'],
            $row['ip_address'],
            $row['created_at']
        ]);
    }
    fclose($out);
    $stmt->close();
    exit;
}

$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Distinct action types for filter dropdown
$distinctActions = $db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC")->fetch_all(MYSQLI_ASSOC);
$distinctActors = $db->query("SELECT DISTINCT actor_email FROM audit_logs ORDER BY actor_email ASC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Audit trail';
$pageCrumb = 'Audit log';
$activeRoute = 'audit';
$portalType = 'admin';

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="admin-shell">
  <?php require dirname(__DIR__) . '/includes/admin_sidebar.php'; ?>

  <div class="admin-main-wrap">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>System audit trail</h1>
          <p>Immutable log of administrative logins, student records management, and event operations.</p>
        </div>
        <div class="header-actions">
          <a class="btn btn-secondary btn-sm" href="?<?= http_build_query(['export' => 1, 'search' => $search, 'action_type' => $actionFilter, 'actor' => $actorFilter]) ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export audit CSV
          </a>
        </div>
      </div>

      <!-- Filter Controls -->
      <section class="card" style="margin-bottom:24px;padding:18px 22px;">
        <form method="get" action="admin_audit.php" style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;">
          <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Search query</label>
            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search details, actor, IP address...">
          </div>

          <div style="width:200px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Action type</label>
            <select name="action_type" class="form-select">
              <option value="">All action types</option>
              <?php foreach ($distinctActions as $a): 
                $meta = app_format_audit_action($a['action']);
              ?>
                <option value="<?= htmlspecialchars($a['action'], ENT_QUOTES, 'UTF-8') ?>" <?= $actionFilter === $a['action'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($meta['label'] . ' (' . $a['action'] . ')', ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="width:220px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Administrator</label>
            <select name="actor" class="form-select">
              <option value="">All administrators</option>
              <?php foreach ($distinctActors as $act): ?>
                <option value="<?= htmlspecialchars($act['actor_email'], ENT_QUOTES, 'UTF-8') ?>" <?= $actorFilter === $act['actor_email'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($act['actor_email'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <button type="submit" class="btn btn-primary" style="height:38px;">Filter log</button>
            <?php if ($search !== '' || $actionFilter !== '' || $actorFilter !== ''): ?>
              <a href="admin_audit.php" class="btn btn-secondary" style="height:38px;">Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <!-- Audit Log Table -->
      <section class="card">
        <div class="card-header">
          <div>
            <h2>Recorded operations (<?= count($logs) ?>)</h2>
            <p>Chronological security log ordered by most recent</p>
          </div>
          <span class="badge badge-info">Tamper Evident</span>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Operation</th>
                <th scope="col">Administrator</th>
                <th scope="col">Details</th>
                <th scope="col">IP Address</th>
                <th scope="col">Recorded timestamp</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $row): 
                  $meta = app_format_audit_action((string)$row['action']);
                ?>
                  <tr data-search="<?= htmlspecialchars($row['actor_email'] . ' ' . $row['action'] . ' ' . $row['details'] . ' ' . $row['ip_address'], ENT_QUOTES, 'UTF-8') ?>">
                    <td class="tabular-nums">#<?= (int)$row['audit_id'] ?></td>
                    <td>
                      <span class="badge <?= $meta['badge'] ?>">
                        <span style="font-size:11px;"><?= $meta['icon'] ?></span>
                        <?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                      <small style="display:block;margin-top:2px;font-size:10px;color:var(--text-light);"><?= htmlspecialchars($row['action'], ENT_QUOTES, 'UTF-8') ?></small>
                    </td>
                    <td><?= htmlspecialchars($row['actor_email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="color:var(--text);"><?= htmlspecialchars($row['details'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="tabular-nums" style="color:var(--text-muted);font-size:12px;"><?= htmlspecialchars($row['ip_address'] ?? '127.0.0.1', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= app_format_datetime($row['created_at']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align:center;padding:36px;color:var(--text-muted);">
                    No audit records match the selected criteria.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

