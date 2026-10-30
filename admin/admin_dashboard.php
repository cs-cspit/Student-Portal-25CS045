<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$admin = $_SESSION['student'] ?? [];
$adminName = (string)($admin['name'] ?? $admin['full_name'] ?? 'Administrator');
$db = app_mysqli();

// Dynamic KPI statistics (Practical 14)
$studentCount = (int)$db->query("SELECT COUNT(*) AS n FROM students WHERE role='Student'")->fetch_assoc()['n'];
$adminCount = (int)$db->query('SELECT COUNT(*) AS n FROM admins')->fetch_assoc()['n'];
$eventCount = (int)$db->query("SELECT COUNT(*) AS n FROM events WHERE status='open'")->fetch_assoc()['n'];
$registrationCount = (int)$db->query("SELECT COUNT(*) AS n FROM registrations WHERE status='registered'")->fetch_assoc()['n'];

// Latest event registrations
$latestRegistrations = $db->query(
    "SELECT r.registered_at, r.status, s.student_id, s.full_name, e.title AS event_title 
     FROM registrations r 
     JOIN students s ON s.student_id=r.student_id 
     JOIN events e ON e.event_id=r.event_id 
     ORDER BY r.registered_at DESC 
     LIMIT 8"
)->fetch_all(MYSQLI_ASSOC);

// Recent audit log entries
$latestAudit = $db->query(
    'SELECT actor_email, action, details, created_at 
     FROM audit_logs 
     ORDER BY created_at DESC, audit_id DESC 
     LIMIT 8'
)->fetch_all(MYSQLI_ASSOC);

// Support & help inquiries count
$helpRequests = app_get_help_requests();
$openInquiryCount = count(array_filter($helpRequests, static fn($t) => ($t['status'] ?? 'open') === 'open'));

$pageTitle = 'Administration overview';
$pageCrumb = 'Overview';
$activeRoute = 'overview';
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
          <h1>Administration overview</h1>
          <p>Real-time campus statistics, latest registrations, and system audit trail.</p>
        </div>
        <div class="header-actions">
          <a href="student_management.php" class="btn btn-secondary btn-sm">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            Add student
          </a>
          <a href="admin_events.php" class="btn btn-primary btn-sm">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create event
          </a>
        </div>
      </div>

      <!-- Welcome Hero Card (Useful & Personal) -->
      <section class="hero-card">
        <div class="hero-content">
          <img src="<?= app_base_url() ?>images/charusat-logo.png" alt="CHARUSAT University Logo" style="height:38px;width:auto;object-fit:contain;margin-bottom:10px;display:block;">
          <h2>Welcome back, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></h2>
          <p>You are signed into the StudentHub Administration panel. All administrative actions are recorded in the system audit log.</p>
        </div>
        <div class="hero-actions">
          <a href="admin_inquiries.php" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
            <span>Support inquiries</span>
            <?php if ($openInquiryCount > 0): ?>
              <span class="badge badge-error" style="font-size:11px;padding:2px 6px;"><?= $openInquiryCount ?></span>
            <?php endif; ?>
          </a>
          <a href="admin_registrations.php" class="btn btn-secondary btn-sm">Review registrations</a>
          <a href="admin_audit.php" class="btn btn-primary btn-sm">View audit trail</a>
        </div>
      </section>

      <!-- Stat Cards: Compact, with Icons and Helper Lines (Fixes Problems 2 & 4) -->
      <section class="stats-grid" aria-label="Administrative metrics">
        <a href="student_management.php" class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total users</span>
            <div class="stat-icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
          </div>
          <div class="stat-value tabular-nums"><?= $studentCount + $adminCount ?></div>
          <div class="stat-helper">
            <span><?= $adminCount ?> Admin · <?= $studentCount ?> Student</span>
          </div>
        </a>

        <a href="student_management.php" class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Students</span>
            <div class="stat-icon-wrap info">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
            </div>
          </div>
          <div class="stat-value tabular-nums"><?= $studentCount ?></div>
          <div class="stat-helper positive">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Active student accounts</span>
          </div>
        </a>

        <a href="admin_events.php" class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Active events</span>
            <div class="stat-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
          </div>
          <div class="stat-value tabular-nums"><?= $eventCount ?></div>
          <div class="stat-helper">
            <span>Published campus programs</span>
          </div>
        </a>

        <a href="admin_registrations.php" class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Registrations</span>
            <div class="stat-icon-wrap success">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
          </div>
          <div class="stat-value tabular-nums"><?= $registrationCount ?></div>
          <div class="stat-helper positive">
            <span>Total reserved places</span>
          </div>
        </a>
      </section>

      <!-- Tables Grid: Latest Registrations & Recent Audit Log -->
      <div class="card-stack">
        <!-- Latest event registrations table -->
        <section class="card" aria-labelledby="registrationsHeading">
          <div class="card-header">
            <div>
              <h2 id="registrationsHeading">Latest event registrations</h2>
              <p>Recent campus event RSVPs submitted by enrolled students</p>
            </div>
            <a href="admin_registrations.php" class="btn btn-secondary btn-sm">View all registrations →</a>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th scope="col">Student</th>
                  <th scope="col">Student ID</th>
                  <th scope="col">Event title</th>
                  <th scope="col">Registered time</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($latestRegistrations)): ?>
                  <?php foreach ($latestRegistrations as $row): ?>
                    <tr>
                      <td>
                        <strong><?= htmlspecialchars((string)$row['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                      </td>
                      <td>
                        <span class="tabular-nums" style="color:var(--text-muted);font-weight:500"><?= htmlspecialchars((string)$row['student_id'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td><?= htmlspecialchars((string)$row['event_title'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= app_format_datetime($row['registered_at']) ?></td>
                      <td><?= app_format_status_badge((string)$row['status']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted)">
                      No event registrations have been recorded yet.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Recent Audit Log Table (Fixes Problem 6: Readable action labels with icons) -->
        <section class="card" aria-labelledby="auditHeading">
          <div class="card-header">
            <div>
              <h2 id="auditHeading">Recent audit log</h2>
              <p>Security and administration operations recorded in real-time</p>
            </div>
            <a href="admin_audit.php" class="btn btn-secondary btn-sm">View full log →</a>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th scope="col">Action</th>
                  <th scope="col">Administrator</th>
                  <th scope="col">Details</th>
                  <th scope="col">Recorded time</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($latestAudit)): ?>
                  <?php foreach ($latestAudit as $row): 
                    $actionMeta = app_format_audit_action((string)$row['action']);
                  ?>
                    <tr>
                      <td>
                        <span class="badge <?= $actionMeta['badge'] ?>">
                          <span style="font-size:11px"><?= $actionMeta['icon'] ?></span>
                          <?= htmlspecialchars($actionMeta['label'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <small style="display:block;margin-top:2px;font-size:10px;color:var(--text-light)"><?= htmlspecialchars((string)$row['action'], ENT_QUOTES, 'UTF-8') ?></small>
                      </td>
                      <td><?= htmlspecialchars((string)$row['actor_email'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td style="color:var(--text-muted);"><?= htmlspecialchars((string)$row['details'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= app_format_datetime($row['created_at']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" style="text-align:center;padding:32px;color:var(--text-muted)">
                      No audit operations recorded yet.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <!-- Access Note Notice (Restyled quiet banner) -->
      <div class="alert alert-info" style="margin-top:24px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <div>
          <strong>Role-Based Access Control Active</strong>
          <div style="font-size:12px;margin-top:2px;color:var(--text-muted)">
            All routes enforce server-side validation. Privileged administrator actions are logged with IP attribution.
          </div>
        </div>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

