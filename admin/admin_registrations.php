<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/audit.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$db = app_mysqli();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['registration_id'])) {
    if (!app_csrf_is_valid()) {
        $message = 'Security token expired. Please try again.';
    } else {
        $regId = (int)$_POST['registration_id'];
        $act = (string)$_POST['action'];
        $newStatus = $act === 'cancel' ? 'cancelled' : 'registered';
        $stmt = $db->prepare('UPDATE registrations SET status = ? WHERE registration_id = ?');
        $stmt->bind_param('si', $newStatus, $regId);
        $stmt->execute();
        $stmt->close();
        app_audit_log('registration.status_update', "Registration ID: {$regId} changed to {$newStatus}");
        $message = 'Registration status updated.';
    }
}

// Fetch all events for filter dropdown
$eventsList = $db->query("SELECT event_id, title FROM events ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

$eventFilter = isset($_GET['event_id']) && $_GET['event_id'] !== '' ? (int)$_GET['event_id'] : null;
$statusFilter = in_array($_GET['status'] ?? '', ['registered', 'cancelled'], true) ? (string)$_GET['status'] : '';
$search = trim((string)($_GET['search'] ?? ''));

$sql = "SELECT r.registration_id, r.registered_at, r.status, s.student_id, s.full_name, s.email, s.course, e.title AS event_title, e.event_date 
        FROM registrations r 
        JOIN students s ON s.student_id = r.student_id 
        JOIN events e ON e.event_id = r.event_id 
        WHERE 1=1";

$params = [];
$types = '';

if ($eventFilter !== null) {
    $sql .= " AND r.event_id = ?";
    $params[] = $eventFilter;
    $types .= 'i';
}
if ($statusFilter !== '') {
    $sql .= " AND r.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}
if ($search !== '') {
    $sql .= " AND (s.student_id LIKE ? OR s.full_name LIKE ? OR s.email LIKE ? OR e.title LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'ssss';
}

$sql .= " ORDER BY r.registered_at DESC";

if (isset($_GET['export'])) {
    $stmt = $db->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $exportRes = $stmt->get_result();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="event-registrations.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Reg ID', 'Student ID', 'Student Name', 'Email', 'Course', 'Event Title', 'Event Date', 'Registered At', 'Status']);
    while ($row = $exportRes->fetch_assoc()) {
        fputcsv($out, [
            $row['registration_id'],
            $row['student_id'],
            $row['full_name'],
            $row['email'],
            $row['course'],
            $row['event_title'],
            $row['event_date'],
            $row['registered_at'],
            $row['status']
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
$registrations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = 'Event registrations';
$pageCrumb = 'Registrations';
$activeRoute = 'registrations';
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
          <h1>Campus event registrations</h1>
          <p>Review and manage all student place reservations across campus programs.</p>
        </div>
        <div class="header-actions">
          <a class="btn btn-secondary btn-sm" href="?<?= http_build_query(['export' => 1, 'search' => $search, 'event_id' => $eventFilter, 'status' => $statusFilter]) ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export registrations CSV
          </a>
        </div>
      </div>

      <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <!-- Filters -->
      <section class="card" style="margin-bottom:24px;padding:18px 22px;">
        <form method="get" action="admin_registrations.php" style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;">
          <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Search keyword</label>
            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search student name, ID, or event">
          </div>

          <div style="width:240px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Filter by event</label>
            <select name="event_id" class="form-select">
              <option value="">All campus events</option>
              <?php foreach ($eventsList as $ev): ?>
                <option value="<?= (int)$ev['event_id'] ?>" <?= $eventFilter === (int)$ev['event_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ev['title'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="width:140px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Status</label>
            <select name="status" class="form-select">
              <option value="">All statuses</option>
              <option value="registered" <?= $statusFilter === 'registered' ? 'selected' : '' ?>>Registered</option>
              <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
          </div>

          <div>
            <button type="submit" class="btn btn-primary" style="height:38px;">Apply filters</button>
            <?php if ($search !== '' || $eventFilter !== null || $statusFilter !== ''): ?>
              <a href="admin_registrations.php" class="btn btn-secondary" style="height:38px;">Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <!-- Registrations Table -->
      <section class="card">
        <div class="card-header">
          <div>
            <h2>Registrations (<?= count($registrations) ?>)</h2>
            <p>Students with RSVP bookings for published events</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th scope="col">Reg ID</th>
                <th scope="col">Student</th>
                <th scope="col">Course</th>
                <th scope="col">Event title</th>
                <th scope="col">Event date</th>
                <th scope="col">Registered timestamp</th>
                <th scope="col">Status</th>
                <th scope="col" style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $r): ?>
                  <tr data-search="<?= htmlspecialchars($r['full_name'] . ' ' . $r['student_id'] . ' ' . $r['event_title'] . ' ' . $r['email'], ENT_QUOTES, 'UTF-8') ?>">
                    <td class="tabular-nums">#<?= (int)$r['registration_id'] ?></td>
                    <td>
                      <strong><?= htmlspecialchars($r['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                      <span class="tabular-nums" style="display:block;font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($r['student_id'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($r['course'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= htmlspecialchars($r['event_title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="tabular-nums"><?= htmlspecialchars($r['event_date'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= app_format_datetime($r['registered_at']) ?></td>
                    <td><?= app_format_status_badge((string)$r['status']) ?></td>
                    <td style="text-align:right;">
                      <?php if ($r['status'] === 'registered'): ?>
                        <form method="post" action="admin_registrations.php" style="display:inline;" onsubmit="return confirm('Cancel this registration?')">
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                          <input type="hidden" name="registration_id" value="<?= (int)$r['registration_id'] ?>">
                          <input type="hidden" name="action" value="cancel">
                          <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--error);border-color:var(--error);">Cancel RSVP</button>
                        </form>
                      <?php else: ?>
                        <form method="post" action="admin_registrations.php" style="display:inline;">
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                          <input type="hidden" name="registration_id" value="<?= (int)$r['registration_id'] ?>">
                          <input type="hidden" name="action" value="restore">
                          <button type="submit" class="btn btn-secondary btn-sm">Reactivate</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" style="text-align:center;padding:36px;color:var(--text-muted);">
                    No event registrations match the selected criteria.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>


