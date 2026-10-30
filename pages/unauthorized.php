<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#15163A">
  <title>403 Access restricted · StudentHub</title>
  <link rel="stylesheet" href="<?= app_base_url() ?>css/tokens.css">
  <link rel="stylesheet" href="<?= app_base_url() ?>css/auth.css">
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
      <a class="auth-brand" href="<?= app_base_url() ?>index.php">
        <span class="brand-mark">S</span>
        <div>
          <span>StudentHub</span>
          <small>ACCESS CONTROL</small>
        </div>
      </a>
      <div class="charusat-topbar-brand" style="margin-left:14px;" title="Charotar University of Science and Technology (CHARUSAT)">
        <img src="<?= app_base_url() ?>images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
      </div>
    </div>
  </header>

  <main style="max-width:640px;margin:80px auto;padding:0 20px;">
    <section class="auth-card" style="text-align:center;padding:44px 32px;">
      <div style="width:56px;height:56px;margin:0 auto 18px;border-radius:50%;background:var(--error-subtle);color:var(--error);display:grid;place-items:center;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
      </div>
      <p class="auth-eyebrow" style="color:var(--error);">HTTP 403 · Unauthorized Access</p>
      <h1 style="font-size:26px;margin:6px 0 14px;">Access is restricted</h1>
      <p style="color:var(--text-muted);font-size:14px;line-height:1.6;margin:0 0 24px;">
        Your authenticated account role does not have permission to open this requested area. Student accounts cannot access the administrative workspace, and administrator accounts use dedicated administrative views.
      </p>
      <div style="display:flex;justify-content:center;gap:12px;">
        <a class="btn btn-primary" href="<?= app_base_url() ?>dashboard.php">Return to your dashboard →</a>
        <form method="post" action="<?= app_base_url() ?>logout.php" style="margin:0;">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-secondary">Sign out</button>
        </form>
      </div>
    </section>
  </main>

  <footer class="auth-footer">
    StudentHub · Role-Based Access Control (RBAC) Enforced
  </footer>
</body>
</html>

