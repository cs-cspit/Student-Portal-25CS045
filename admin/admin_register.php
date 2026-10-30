<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/audit.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$errors = [];
$name = '';
$email = '';
$notice = (string)($_SESSION['admin_registration_notice'] ?? '');
unset($_SESSION['admin_registration_notice']);

$db = app_mysqli();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (!app_csrf_is_valid()) {
        $errors[] = 'The form expired. Please try again.';
    }
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Enter the administrator’s full name (up to 100 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8 || strlen($password) > 200) {
        $errors[] = 'Use a password between 8 and 200 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'The password and confirmation do not match.';
    }

    if (!$errors) {
        try {
            $check = $db->prepare('SELECT email FROM students WHERE email = ? UNION SELECT email FROM admins WHERE email = ? LIMIT 1');
            $check->bind_param('ss', $email, $email);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();
            $check->close();

            if ($exists) {
                $errors[] = 'That email is already in use. Use a different email address.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $db->prepare('INSERT INTO admins (full_name, email, password_hash) VALUES (?, ?, ?)');
                $insert->bind_param('sss', $name, $email, $hash);
                $insert->execute();
                $insert->close();
                app_audit_log('admin.create', 'Administrator email: ' . $email);
                $_SESSION['admin_registration_notice'] = 'The administrator account was created successfully.';
                header('Location: admin_register.php', true, 303);
                exit;
            }
        } catch (Throwable $error) {
            error_log('StudentHub admin registration failed: ' . $error->getMessage());
            $errors[] = 'Could not create the account. Confirm migration 003 has been applied and try again.';
        }
    }
}

// Fetch existing admin accounts
$adminList = [];
try {
    $adminList = $db->query('SELECT admin_id, full_name, email, created_at FROM admins ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    // If table doesn't have created_at or similar
    $adminList = $db->query('SELECT admin_id, full_name, email FROM admins')->fetch_all(MYSQLI_ASSOC);
}

$pageTitle = 'Add administrator';
$pageCrumb = 'Add administrator';
$activeRoute = 'admin_register';
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
          <h1>Add administrator account</h1>
          <p>Create privileged access accounts for faculty staff and department officers.</p>
        </div>
      </div>

      <?php if ($notice !== ''): ?>
        <div class="alert alert-success" role="status"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
          <ul style="margin:0;padding-left:18px;">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars((string)$err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="two-column-layout">
        <!-- Account Registration Form -->
        <section class="card">
          <div class="card-header">
            <h2>New administrator credentials</h2>
            <span class="badge badge-warning">Restricted</span>
          </div>
          <div class="card-body">
            <form method="post" action="admin_register.php" autocomplete="off">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

              <div class="form-group">
                <label for="name">Full name</label>
                <input id="name" name="name" class="form-control" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. Prof. David Miller" required>
              </div>

              <div class="form-group">
                <label for="email">Institutional email address</label>
                <input id="email" name="email" type="email" class="form-control" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" placeholder="admin@example.edu" required>
              </div>

              <div class="form-group">
                <label for="password">Password (minimum 8 characters)</label>
                <input id="password" name="password" type="password" class="form-control" minlength="8" placeholder="Create a strong password" required>
              </div>

              <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input id="confirm_password" name="confirm_password" type="password" class="form-control" minlength="8" placeholder="Re-enter password" required>
              </div>

              <button type="submit" class="btn btn-primary" style="margin-top:12px;">Create administrator account</button>
            </form>
          </div>
        </section>

        <!-- Current Administrators List -->
        <section class="card">
          <div class="card-header">
            <div>
              <h2>Current administrators (<?= count($adminList) ?>)</h2>
              <p>Staff members with administrative permissions</p>
            </div>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th scope="col">Staff name</th>
                  <th scope="col">Email address</th>
                  <th scope="col">Role</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($adminList as $a): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars((string)$a['full_name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars((string)$a['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-success"><span class="badge-dot"></span> Administrator</span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
