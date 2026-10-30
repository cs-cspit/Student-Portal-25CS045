<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
app_no_cache_headers();

if (!empty($_SESSION['student']) && is_array($_SESSION['student'])) {
    $lastActivity = (int)($_SESSION['last_activity'] ?? 0);
    if ($lastActivity > 0 && time() - $lastActivity <= APP_SESSION_TIMEOUT) {
        header('Location: ' . app_dashboard_url((string)($_SESSION['student']['role'] ?? 'Student')), true, 303);
        exit;
    }
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['login_notice'] = 'Your session expired due to inactivity. Please sign in again.';
}

$errors = $_SESSION['login_errors'] ?? [];
unset($_SESSION['login_errors']);
$notice = (string)($_SESSION['login_notice'] ?? '');
unset($_SESSION['login_notice']);
if (isset($_GET['logged_out'])) {
    $notice = 'You have been logged out. Sign in again to continue.';
}
$captchaA = (int)($_SESSION['captcha_a'] ?? 0);
$captchaB = (int)($_SESSION['captcha_b'] ?? 0);
if ($captchaA < 2 || isset($_GET['refresh_captcha'])) {
    $captchaA = random_int(2, 9);
    $captchaB = random_int(1, 9);
    $_SESSION['captcha_a'] = $captchaA;
    $_SESSION['captcha_b'] = $captchaB;
}
$identifier = '';
$captchaQuestion = $captchaA . ' + ' . $captchaB;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log in · StudentHub</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">
  <header class="auth-header"><a class="auth-brand" href="index.php"><span class="brand-mark">S</span><span>StudentHub<small>STUDENT PORTAL</small></span></a><p>New to StudentHub? <a href="register.php">Create an account</a></p></header>
  <main class="auth-main auth-main-login">
    <section class="auth-intro"><p class="auth-eyebrow">STUDENT ACCESS</p><h1>Welcome back</h1><p>Sign in to continue to your academic workspace.</p><div class="login-info"><span class="info-mark">i</span><p>Use your registered email address or student ID and password.</p></div><div class="login-points"><span>Courses and study schedule</span><span>Assignments and progress</span><span>Student profile and notices</span></div></section>
    <section class="auth-card login-card"><div class="auth-card-heading"><h2>Log in to your account</h2><p>Enter your account details below.</p></div>
      <?php if ($notice !== ''): ?><div class="form-alert form-alert-success" role="status"><?= app_escape($notice) ?></div><?php endif; ?>
      <?php if ($errors): ?><div class="form-alert form-alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= app_escape((string)$error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
      <form method="post" action="authenticate.php" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>">
        <div class="field"><label for="identifier">Email address or student ID</label><input id="identifier" name="identifier" type="text" autocomplete="username" value="<?= app_escape($identifier) ?>" required autofocus></div>
        <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
        <div class="captcha-field"><div><label for="captcha">Security check</label><p class="field-note">Enter the answer to verify this sign-in.</p></div><div class="captcha-equation"><span><?= app_escape($captchaQuestion) ?> =</span><input id="captcha" name="captcha" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" aria-label="Answer to the security check" required></div></div>
        <a class="captcha-refresh" href="login.php?refresh_captcha=1">Get a different question</a>
        <button class="auth-submit" type="submit">Log in <span aria-hidden="true">→</span></button>
      </form>
      <p class="auth-switch">Need a student account? <a href="register.php">Register here</a></p>
    </section>
  </main>
  <footer class="auth-footer">StudentHub · Student account access</footer>
</body>
</html>
