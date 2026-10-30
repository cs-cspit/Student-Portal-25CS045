<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/db.php';
app_require_role('Admin');
app_no_cache_headers();

$errors = [];
$name = '';
$email = '';

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
            $db = app_mysqli();
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
$notice = (string)($_SESSION['admin_registration_notice'] ?? '');
unset($_SESSION['admin_registration_notice']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add administrator · StudentHub</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">
  <header class="auth-header"><a class="auth-brand" href="admin_dashboard.php"><span class="brand-mark">S</span><span>StudentHub<small>ADMINISTRATION</small></span></a><p><a href="admin_dashboard.php">Return to dashboard</a></p></header>
  <main class="auth-main auth-main-login">
    <section class="auth-intro"><p class="auth-eyebrow">ADMINISTRATION</p><h1>Add an administrator</h1><p>Create a separate administrator account. Student ID and course details are not required.</p></section>
    <section class="auth-card login-card"><div class="auth-card-heading"><h2>Administrator details</h2><p>Only a signed-in administrator can create another administrator account.</p></div>
      <?php if ($notice !== ''): ?><div class="form-alert form-alert-success" role="status"><?= app_escape($notice) ?></div><?php endif; ?>
      <?php if ($errors): ?><div class="form-alert form-alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= app_escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
      <form method="post" action="admin_register.php" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>">
        <div class="field"><label for="name">Full name</label><input id="name" name="name" type="text" maxlength="100" value="<?= app_escape($name) ?>" autocomplete="name" required></div>
        <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" maxlength="254" value="<?= app_escape($email) ?>" autocomplete="email" required></div>
        <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required></div>
        <div class="field"><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
        <button class="auth-submit" type="submit">Create administrator</button>
      </form>
    </section>
  </main>
  <footer class="auth-footer">StudentHub · Restricted administrator setup</footer>
</body>
</html>
