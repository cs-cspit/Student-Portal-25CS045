<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
app_no_cache_headers();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#15163A">
  <title>About StudentHub · University Academic Portal</title>
  <link rel="stylesheet" href="../css/tokens.css">
  <link rel="stylesheet" href="../css/auth.css">
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
<body class="landing-page">
  <header class="landing-header">
    <div style="display:flex;align-items:center;gap:14px;">
      <a class="auth-brand" href="../index.php">
        <span class="brand-mark">S</span>
        <div>
          <span>StudentHub</span>
          <small>ABOUT PORTAL</small>
        </div>
      </a>
      <div class="charusat-topbar-brand" style="margin-left:14px;" title="Charotar University of Science and Technology (CHARUSAT)">
        <img src="../images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
      </div>
    </div>
    <nav style="display:flex;align-items:center;gap:18px;">
      <a class="landing-nav-link" href="../index.php">Home</a>
      <a class="landing-nav-link" href="faq.php">FAQs</a>
      <a class="landing-nav-link" href="contact.php">Contact</a>
      <a class="btn btn-primary" href="../login.php">Sign in</a>
    </nav>
  </header>

  <main style="width:min(100% - 48px, 1000px);margin:0 auto;padding:48px 0 64px;">
    <div style="max-width:760px;margin-bottom:36px;">
      <p class="auth-eyebrow">Academic Architecture</p>
      <h1 style="font-size:36px;margin:8px 0 16px;">Built for university clarity and streamlined administration.</h1>
      <p style="font-size:16px;color:var(--text-muted);line-height:1.7;">
        StudentHub is a unified full-stack academic platform integrating coursework tracking, lecture timetables, assignments management, real-time attendance registers, and role-protected administrative tools.
      </p>
    </div>

    <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:24px;margin-bottom:48px;">
      <article class="auth-card">
        <h2 style="font-size:18px;margin:0 0 10px;">Role-Based Access (RBAC)</h2>
        <p style="color:var(--text-muted);font-size:14px;line-height:1.6;">
          Zero-trust server-side session authentication with strictly enforced route boundaries separating Student workspaces from privileged Administrator controls.
        </p>
      </article>

      <article class="auth-card">
        <h2 style="font-size:18px;margin:0 0 10px;">Asynchronous Event CRUD</h2>
        <p style="color:var(--text-muted);font-size:14px;line-height:1.6;">
          Modern client-side Fetch API integration powering instant campus event RSVP registrations and administrator schedule publishing without full page reloads.
        </p>
      </article>

      <article class="auth-card">
        <h2 style="font-size:18px;margin:0 0 10px;">Tamper-Evident Audit Trail</h2>
        <p style="color:var(--text-muted);font-size:14px;line-height:1.6;">
          Every administrative record insertion, modification, and deletion is recorded with operator identity, IP attribution, and immutable server timestamps.
        </p>
      </article>
    </div>

    <section class="auth-card" style="padding:32px;">
      <h2 style="font-size:22px;margin:0 0 14px;">Technical Foundations</h2>
      <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin:0 0 20px;">
        StudentHub runs on an enterprise PHP 8+ architecture backed by MySQL/MariaDB with dual PDO and MySQLi prepared statement drivers, secure password hashing (BCRYPT), cross-site request forgery (CSRF) protection, and WCAG 2.2 AA accessibility standards.
      </p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <span class="badge badge-info">PHP 8+</span>
        <span class="badge badge-info">MySQL / MariaDB</span>
        <span class="badge badge-info">Prepared Statements</span>
        <span class="badge badge-info">CSRF Protection</span>
        <span class="badge badge-info">Dark Mode Tokens</span>
        <span class="badge badge-info">Fetch API</span>
      </div>
    </section>
  </main>

  <footer class="landing-footer">
    <div><strong>StudentHub</strong> · University Portal</div>
    <div style="display:flex;gap:16px;">
      <a href="../index.php">Home</a>
      <a href="faq.php">FAQs</a>
      <a href="contact.php">Contact</a>
    </div>
  </footer>
</body>
</html>

