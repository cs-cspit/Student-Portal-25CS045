<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/audit.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$admin = $_SESSION['student'] ?? [];
$adminName = (string)($admin['name'] ?? $admin['full_name'] ?? 'Administrator');
$adminEmail = (string)($admin['email'] ?? 'admin.demo@example.edu');
$initials = app_get_initials($adminName);

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    if (!app_csrf_is_valid()) {
        $errorMsg = 'Form session expired. Please refresh and try again.';
    } else {
        $currentPwd = (string)($_POST['current_password'] ?? '');
        $newPwd = (string)($_POST['new_password'] ?? '');
        $confirmPwd = (string)($_POST['confirm_password'] ?? '');

        if ($newPwd === '' || strlen($newPwd) < 8) {
            $errorMsg = 'New password must be at least 8 characters long.';
        } elseif ($newPwd !== $confirmPwd) {
            $errorMsg = 'New password and confirmation do not match.';
        } else {
            try {
                $db = app_mysqli();
                $stmt = $db->prepare('SELECT password_hash FROM admins WHERE email = ? LIMIT 1');
                $stmt->bind_param('s', $adminEmail);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($res && password_verify($currentPwd, (string)$res['password_hash'])) {
                    $newHash = password_hash($newPwd, PASSWORD_DEFAULT);
                    $upd = $db->prepare('UPDATE admins SET password_hash = ? WHERE email = ?');
                    $upd->bind_param('ss', $newHash, $adminEmail);
                    $upd->execute();
                    $upd->close();
                    app_audit_log('admin.password_change', "Administrator {$adminEmail} updated password");
                    $successMsg = 'Administrator password has been successfully updated.';
                } else {
                    $errorMsg = 'Current password is not correct.';
                }
            } catch (Throwable $e) {
                $errorMsg = 'Database error updating password.';
            }
        }
    }
}

$pageTitle = 'Administration settings';
$pageCrumb = 'Settings';
$activeRoute = 'settings';
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
          <h1>System settings & security</h1>
          <p>Administrator credentials, session timeout configuration, and server diagnostics.</p>
        </div>
      </div>

      <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <?php if ($errorMsg): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="two-column-layout">
        <!-- Admin Profile & Password -->
        <div class="card-stack">
          <section class="card">
            <div class="card-header">
              <h2>Administrator profile</h2>
            </div>
            <div class="card-body">
              <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                <div class="avatar" style="width:54px;height:54px;font-size:18px;">
                  <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div>
                  <h3 style="margin:0 0 4px;font-size:17px;"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></h3>
                  <p style="margin:0;color:var(--text-muted);font-size:13px;"><?= htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              </div>
              <div style="padding:10px 0;border-top:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                <span style="font-size:13px;color:var(--text-muted);">Access level:</span>
                <span class="badge badge-success"><span class="badge-dot"></span> Full Administrator</span>
              </div>
            </div>
          </section>

          <section class="card">
            <div class="card-header">
              <h2>Change administrator password</h2>
            </div>
            <div class="card-body">
              <form method="post" action="admin_settings.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                  <label for="current_password">Current password</label>
                  <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                  <label for="new_password">New password (minimum 8 characters)</label>
                  <input type="password" id="new_password" name="new_password" class="form-control" minlength="8" required>
                </div>

                <div class="form-group">
                  <label for="confirm_password">Confirm new password</label>
                  <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="8" required>
                </div>

                <button type="submit" class="btn btn-primary">Update admin password</button>
              </form>
            </div>
          </section>
        </div>

        <!-- System & Security Diagnostics -->
        <div class="card-stack">
          <section class="card">
            <div class="card-header">
              <h2>System diagnostics & environment</h2>
            </div>
            <div class="card-body">
              <div class="grid" style="grid-template-columns:1fr;gap:12px;">
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">PHP Version:</span>
                  <strong>PHP <?= phpversion() ?></strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Database Driver:</span>
                  <strong>MySQLi & PDO (MySQL/MariaDB)</strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Session Inactivity Timeout:</span>
                  <strong class="tabular-nums">15 minutes (900s)</strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Audit Trail:</span>
                  <span class="badge badge-success">Active & Logging</span>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Upload Directory:</span>
                  <code>uploads/event-posters/</code>
                </div>
              </div>
            </div>
          </section>

          <section class="card">
            <div class="card-header">
              <h2>Quick maintenance links</h2>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
              <a href="../database/test_connection.php" class="btn btn-secondary btn-sm" style="justify-content:flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                Verify database connection diagnostics
              </a>
              <a href="admin_audit.php" class="btn btn-secondary btn-sm" style="justify-content:flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                View complete system audit log
              </a>
            </div>
          </section>
        </div>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>


