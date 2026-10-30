<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/db.php';
app_require_role('Admin');
$admin = $_SESSION['student'];
$db = app_mysqli();
$studentCount = (int)$db->query("SELECT COUNT(*) AS n FROM students WHERE role='Student'")->fetch_assoc()['n'];
$adminCount = (int)$db->query('SELECT COUNT(*) AS n FROM admins')->fetch_assoc()['n'];
$eventCount = (int)$db->query("SELECT COUNT(*) AS n FROM events WHERE status='open'")->fetch_assoc()['n'];
$registrationCount = (int)$db->query("SELECT COUNT(*) AS n FROM registrations WHERE status='registered'")->fetch_assoc()['n'];
$latestRegistrations = $db->query("SELECT r.registered_at,r.status,s.student_id,s.full_name,e.title AS event_title FROM registrations r JOIN students s ON s.student_id=r.student_id JOIN events e ON e.event_id=r.event_id ORDER BY r.registered_at DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$latestAudit = $db->query('SELECT actor_email,action,details,created_at FROM audit_logs ORDER BY created_at DESC,audit_id DESC LIMIT 10')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#132238">
  <title>Administration · StudentHub</title>
  <link rel="stylesheet" href="css/studyhub.css">
  <link rel="stylesheet" href="practicals/practical10/style.css">
</head>
<body class="admin-page">
  <div class="admin-shell">
    <aside class="admin-rail">
      <a class="admin-brand" href="admin_dashboard.php"><span class="brand-mark">S</span><span>StudentHub<small>ADMINISTRATION</small></span></a>
      <p class="admin-nav-label">ADMINISTRATION</p>
      <a class="admin-nav-link is-active" href="admin_dashboard.php"><span>▦</span> Overview</a>
      <a class="admin-nav-link" href="student_management.php"><span>♙</span> Students</a>
      <a class="admin-nav-link" href="admin_events.php"><span>✳</span> Events</a>
      <a class="admin-nav-link" href="admin_register.php"><span>＋</span> Add administrator</a>
      <a class="admin-nav-link" href="index.php"><span>⌂</span> Public home</a>
      <div class="admin-rail-bottom">
        <span class="admin-role-pill">Administrator</span>
        <form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>"><button class="admin-logout" type="submit">Log out</button></form>
      </div>
    </aside>
    <main class="admin-main">
      <header class="admin-topbar"><div><p class="admin-eyebrow">STUDENTHUB · ACCESS CONTROL</p><h1>Administration overview</h1></div><span class="admin-avatar" aria-hidden="true"><?= app_escape(strtoupper(substr((string)$admin['name'], 0, 1))) ?></span></header>
      <section class="admin-welcome"><p class="admin-eyebrow">ADMINISTRATOR WORKSPACE</p><h2>Welcome, <?= app_escape((string)$admin['name']) ?></h2><p>This area is available to Admin accounts. Student accounts are blocked from the administration dashboard.</p></section>
      <section class="admin-grid" aria-label="Administration summary">
        <article class="admin-card"><span class="admin-card-icon">01</span><p>Total users</p><strong><?= $studentCount + $adminCount ?></strong><small><?= $adminCount ?> Admin · <?= $studentCount ?> Student</small></article>
        <article class="admin-card"><span class="admin-card-icon">02</span><p>Students</p><strong><?= $studentCount ?></strong><small>Registered student accounts</small></article>
        <article class="admin-card"><span class="admin-card-icon">03</span><p>Active events</p><strong><?= $eventCount ?></strong><small>Open event records</small></article>
        <article class="admin-card"><span class="admin-card-icon">04</span><p>Registrations</p><strong><?= $registrationCount ?></strong><small>Current registered places</small></article>
      </section>
      <section class="admin-note"><strong>Latest event registrations</strong><div style="overflow:auto"><table class="admin-data-table"><thead><tr><th>Student</th><th>Event</th><th>Registered</th><th>Status</th></tr></thead><tbody><?php foreach ($latestRegistrations as $row): ?><tr><td><?= app_escape($row['full_name']) ?> (<?= app_escape($row['student_id']) ?>)</td><td><?= app_escape($row['event_title']) ?></td><td><?= app_escape($row['registered_at']) ?></td><td><?= app_escape($row['status']) ?></td></tr><?php endforeach; ?><?php if (!$latestRegistrations): ?><tr><td colspan="4">No registrations yet.</td></tr><?php endif; ?></tbody></table></div></section>
      <section class="admin-note"><strong>Recent audit log</strong><div style="overflow:auto"><table class="admin-data-table"><thead><tr><th>Admin</th><th>Action</th><th>Details</th><th>Time</th></tr></thead><tbody><?php foreach ($latestAudit as $row): ?><tr><td><?= app_escape($row['actor_email']) ?></td><td><?= app_escape($row['action']) ?></td><td><?= app_escape($row['details']) ?></td><td><?= app_escape($row['created_at']) ?></td></tr><?php endforeach; ?><?php if (!$latestAudit): ?><tr><td colspan="4">No logged Admin actions yet.</td></tr><?php endif; ?></tbody></table></div></section>
      <style>.admin-data-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:11px}.admin-data-table th,.admin-data-table td{padding:9px 10px;border-bottom:1px solid #dce8e7;text-align:left;white-space:nowrap}.admin-data-table th{color:#536b70}</style>
      <aside class="admin-note"><strong>Access is role protected</strong><p>Dashboard routes validate the signed-in role on every request. Sign out when you finish using this account.</p></aside>
    </main>
  </div>
  <script>window.addEventListener('pageshow', event => { if (event.persisted) window.location.reload(); });</script>
</body>
</html>
