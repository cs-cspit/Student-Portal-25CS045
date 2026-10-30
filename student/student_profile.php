<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'My profile';
$pageCrumb = 'Profile';
$activeRoute = 'profile';
$portalType = 'student';

$student = $_SESSION['student'] ?? [];
$studentName = (string)($student['name'] ?? $student['full_name'] ?? 'Student');
$studentId = (string)($student['student_id'] ?? '');
$studentEmail = (string)($student['email'] ?? '');
$studentPhone = (string)($student['phone'] ?? '');
$studentCourse = (string)($student['course'] ?? 'B.Tech');
$studentAge = (string)($student['age'] ?? '20');
$studentGender = (string)($student['gender'] ?? 'Not specified');
$initials = app_get_initials($studentName);

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    if (!app_csrf_is_valid()) {
        $errorMsg = 'Security token expired. Please try again.';
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
                $stmt = $db->prepare('SELECT password_hash FROM students WHERE student_id = ? LIMIT 1');
                $stmt->bind_param('s', $studentId);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($res && password_verify($currentPwd, (string)$res['password_hash'])) {
                    $newHash = password_hash($newPwd, PASSWORD_DEFAULT);
                    $upd = $db->prepare('UPDATE students SET password_hash = ? WHERE student_id = ?');
                    $upd->bind_param('ss', $newHash, $studentId);
                    $upd->execute();
                    $upd->close();
                    $successMsg = 'Your password has been successfully updated.';
                } else {
                    $errorMsg = 'Current password is incorrect.';
                }
            } catch (Throwable $e) {
                $errorMsg = 'Database error updating password.';
            }
        }
    }
}

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>Student profile & settings</h1>
          <p>Review your personal records, contact credentials, and security preferences.</p>
        </div>
      </div>

      <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <?php if ($errorMsg): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="two-column-layout">
        <!-- Profile Overview Card -->
        <div class="card-stack">
          <section class="card" style="padding:28px;text-align:center;">
            <div class="avatar" style="width:76px;height:76px;margin:0 auto 16px;font-size:26px;">
              <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <h2 style="margin:0 0 6px;font-size:20px;"><?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?></h2>
            <p style="margin:0 0 16px;color:var(--text-muted);font-size:14px;"><?= htmlspecialchars($studentCourse, ENT_QUOTES, 'UTF-8') ?></p>
            <div style="display:flex;justify-content:center;gap:8px;">
              <span class="badge badge-success"><span class="badge-dot"></span> Active enrollment</span>
              <span class="badge badge-neutral">Fall 2026</span>
            </div>
          </section>

          <section class="card">
            <div class="card-header">
              <h2>Verified student record</h2>
            </div>
            <div class="card-body">
              <div class="grid" style="grid-template-columns:1fr;gap:12px;">
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Student ID:</span>
                  <strong class="tabular-nums"><?= htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Registered email:</span>
                  <strong><?= htmlspecialchars($studentEmail, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Mobile number:</span>
                  <strong class="tabular-nums"><?= htmlspecialchars($studentPhone, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Age:</span>
                  <strong class="tabular-nums"><?= htmlspecialchars($studentAge, ENT_QUOTES, 'UTF-8') ?> years</strong>
                </div>
                <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;">
                  <span style="color:var(--text-muted);font-size:13px;">Gender:</span>
                  <strong><?= htmlspecialchars($studentGender, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Security & Preferences Card -->
        <div class="card-stack">
          <section class="card">
            <div class="card-header">
              <h2>Change account password</h2>
              <p>Ensure your account uses a secure password</p>
            </div>
            <div class="card-body">
              <form method="post" action="student_profile.php">
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

                <button type="submit" class="btn btn-primary">Update password</button>
              </form>
            </div>
          </section>

          <section class="card">
            <div class="card-header">
              <h2>Session security</h2>
            </div>
            <div class="card-body">
              <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;">
                Idle session timeout is set to 15 minutes. To safeguard your account, always log out when accessing StudentHub from public campus terminals.
              </p>
              <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-secondary btn-sm">Sign out of this session</button>
              </form>
            </div>
          </section>
        </div>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

