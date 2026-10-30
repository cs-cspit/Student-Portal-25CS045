<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
app_no_cache_headers();

// If already signed in with an active session, redirect to the appropriate workspace
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
    $notice = 'You have been safely signed out. Sign in again to continue.';
}

$captchaA = (int)($_SESSION['captcha_a'] ?? 0);
$captchaB = (int)($_SESSION['captcha_b'] ?? 0);
if ($captchaA < 2 || isset($_GET['refresh_captcha'])) {
    $captchaA = random_int(2, 9);
    $captchaB = random_int(1, 9);
    $_SESSION['captcha_a'] = $captchaA;
    $_SESSION['captcha_b'] = $captchaB;
}
$captchaQuestion = $captchaA . ' + ' . $captchaB;
$identifier = (string)($_SESSION['login_identifier'] ?? '');
unset($_SESSION['login_identifier']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in · StudentHub</title>
  <link rel="stylesheet" href="css/tokens.css">
  <link rel="stylesheet" href="css/auth.css">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('studenthub_theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = savedTheme ? savedTheme : (prefersDark ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
      if (theme === 'dark') document.documentElement.classList.add('dark');
    })();
  </script>
</head>
<body class="auth-page">
  <header class="auth-header">
    <div style="display:flex;align-items:center;gap:14px;">
      <a class="auth-brand" href="index.php" aria-label="StudentHub home">
        <span class="brand-mark">S</span>
        <div>
          <span>StudentHub</span>
          <small>PORTAL ACCESS</small>
        </div>
      </a>
      <div class="charusat-topbar-brand" style="margin-left:14px;" title="Charotar University of Science and Technology (CHARUSAT)">
        <img src="images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
      <button id="themeToggleBtn" class="btn-icon-toggle" type="button" aria-label="Toggle dark mode" style="width:36px;height:36px;border-radius:8px;background:var(--background);border:1px solid var(--border);color:var(--text);cursor:pointer;display:grid;place-items:center;">
        <svg class="theme-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        <svg class="theme-icon-moon" style="display:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
      </button>
      <p style="margin:0;font-size:13px;color:var(--text-muted)">New to StudentHub? <a href="register.php" style="color:var(--primary);font-weight:600">Create an account</a></p>
    </div>
  </header>

  <main class="auth-main auth-main-login">
    <section class="auth-intro">
      <p class="auth-eyebrow">Account Access</p>
      <h1>Welcome back</h1>
      <p>Sign in to your unified StudentHub account. Your role directs you automatically to either the Student academic workspace or the Administrator overview.</p>
      
      <div class="login-info">
        <span class="info-mark">i</span>
        <p>Students can sign in using their registered email or Student ID. Administrators sign in using their registered email.</p>
      </div>

      <div class="login-points">
        <span>Enrolled courses, schedules, and class assignments</span>
        <span>Attendance records, semester grades, and academic notices</span>
        <span>Campus opportunities, event registrations, and administration</span>
      </div>
    </section>

    <section class="auth-card login-card">
      <div class="auth-card-heading">
        <h2>Sign in to your account</h2>
        <p>Enter your account credentials and security verification below.</p>
      </div>

      <?php if ($notice !== ''): ?>
        <div class="form-alert form-alert-success" role="status"><?= app_escape($notice) ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="form-alert form-alert-error" role="alert">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= app_escape((string)$err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="authenticate.php" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>">

        <div class="field">
          <label for="identifier">Email address or Student ID</label>
          <input id="identifier" name="identifier" type="text" autocomplete="username" value="<?= app_escape($identifier) ?>" placeholder="student@example.edu or 25CS001" required autofocus>
        </div>

        <div class="field">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <label for="password" style="margin:0">Password</label>
            <span id="capsWarning" style="display:none;color:var(--warning);font-size:11px;font-weight:600">Caps Lock is on</span>
          </div>
          <div style="position:relative">
            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required style="padding-right:40px">
            <button type="button" id="togglePasswordBtn" aria-label="Show password" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;display:grid;place-items:center;">
              <svg id="eyeOpenIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg id="eyeClosedIcon" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
            </button>
          </div>
        </div>

        <div class="captcha-field">
          <div>
            <label for="captcha">Security check</label>
            <p class="field-note">Solve the simple arithmetic verification.</p>
          </div>
          <div class="captcha-equation">
            <span><?= app_escape($captchaQuestion) ?> =</span>
            <input id="captcha" name="captcha" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="?" aria-label="Answer to arithmetic question" required>
          </div>
        </div>

        <div style="margin-top:6px;text-align:right">
          <a class="captcha-refresh" href="login.php?refresh_captcha=1" style="color:var(--primary);font-size:12px;font-weight:500">↻ Get a different question</a>
        </div>

        <button class="auth-submit" type="submit">
          <span>Sign in to account</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
      </form>

      <p class="auth-switch">Need a new student account? <a href="register.php">Register online</a></p>
    </section>
  </main>

  <footer class="auth-footer">
    StudentHub · Secure Single Sign-In Gateway · Role Protected
  </footer>

  <script>
    // Password visibility toggle
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpenIcon');
    const eyeClosed = document.getElementById('eyeClosedIcon');

    if (toggleBtn && pwdInput) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.getAttribute('type') === 'password';
        pwdInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeOpen.style.display = isPassword ? 'none' : 'block';
        eyeClosed.style.display = isPassword ? 'block' : 'none';
        toggleBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
      });
    }

    // Caps Lock indicator
    const capsWarning = document.getElementById('capsWarning');
    if (pwdInput && capsWarning) {
      pwdInput.addEventListener('keyup', (e) => {
        if (e.getModifierState && e.getModifierState('CapsLock')) {
          capsWarning.style.display = 'inline';
        } else {
          capsWarning.style.display = 'none';
        }
      });
    }

    // Theme toggle
    const themeBtn = document.getElementById('themeToggleBtn');
    const sunIcon = document.querySelector('.theme-icon-sun');
    const moonIcon = document.querySelector('.theme-icon-moon');

    function updateThemeUI(t) {
      document.documentElement.setAttribute('data-theme', t);
      if (t === 'dark') {
        document.documentElement.classList.add('dark');
        if (sunIcon) sunIcon.style.display = 'none';
        if (moonIcon) moonIcon.style.display = 'block';
      } else {
        document.documentElement.classList.remove('dark');
        if (sunIcon) sunIcon.style.display = 'block';
        if (moonIcon) moonIcon.style.display = 'none';
      }
    }

    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateThemeUI(currentTheme);

    if (themeBtn) {
      themeBtn.addEventListener('click', () => {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('studenthub_theme', next);
        updateThemeUI(next);
      });
    }
  </script>
</body>
</html>

