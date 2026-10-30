<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
app_no_cache_headers();

$isLoggedIn = !empty($_SESSION['student']) && is_array($_SESSION['student']);
$userRole = (string)($_SESSION['student']['role'] ?? 'Student');
$dashboardUrl = app_dashboard_url($userRole);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#15163A">
  <meta name="description" content="StudentHub is a focused workspace for student courses, schedules, assignments, attendance, and campus events.">
  <title>StudentHub · Student learning & administration portal</title>
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
<body class="landing-page">
  <header class="landing-header">
    <div style="display:flex;align-items:center;gap:14px;">
      <a class="auth-brand" href="index.php" aria-label="StudentHub home">
        <span class="brand-mark">S</span>
        <div>
          <span>StudentHub</span>
          <small>UNIVERSITY PORTAL</small>
        </div>
      </a>
      <div class="charusat-topbar-brand" style="margin-left:14px;" title="Charotar University of Science and Technology (CHARUSAT)">
        <img src="images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
      </div>
    </div>
    <nav style="display:flex;align-items:center;gap:18px;">
      <a class="landing-nav-link" href="pages/about.php">About</a>
      <a class="landing-nav-link" href="pages/faq.php">FAQs</a>
      <a class="landing-nav-link" href="pages/contact.php">Contact</a>
      <button id="themeToggleBtn" class="btn-icon-toggle" type="button" aria-label="Toggle dark mode" style="width:36px;height:36px;border-radius:8px;background:var(--background);border:1px solid var(--border);color:var(--text);cursor:pointer;display:grid;place-items:center;">
        <svg class="theme-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        <svg class="theme-icon-moon" style="display:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
      </button>

      <?php if ($isLoggedIn): ?>
        <a class="btn btn-primary" href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8') ?>">Go to dashboard →</a>
      <?php else: ?>
        <a class="landing-nav-link" href="login.php">Sign in</a>
        <a class="btn btn-primary" href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <section class="landing-hero">
      <div class="landing-copy">
        <p class="auth-eyebrow">Academic Workspace & Administration</p>
        <h1>Make space for<br><span>what you’re learning.</span></h1>
        <p class="landing-description">A unified, elegant university platform to stay on top of enrolled courses, lecture schedules, lab deliverables, attendance standing, and campus programs.</p>
        <div class="landing-actions">
          <?php if ($isLoggedIn): ?>
            <a class="auth-submit landing-primary" href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8') ?>">
              <span>Open your workspace</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
          <?php else: ?>
            <a class="auth-submit landing-primary" href="login.php">
              <span>Sign in to StudentHub</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
            <a class="btn btn-secondary" href="register.php">Create student account</a>
          <?php endif; ?>
        </div>
        <p class="field-note" style="margin-top:14px;">Students register with university ID and email. Admins access system management via single sign-in.</p>
      </div>
    </section>

    <section class="landing-features">
      <article>
        <span class="feature-number">01</span>
        <h2>Unified academic workspace</h2>
        <p>Keep enrolled coursework, professor office hours, syllabus modules, and course deliverables neatly synchronized.</p>
      </article>

      <article>
        <span class="feature-number">02</span>
        <h2>Timetable & attendance</h2>
        <p>Dynamic day-by-day timetable with room indicators and real-time attendance tracking ensuring 75% university eligibility.</p>
      </article>

      <article>
        <span class="feature-number">03</span>
        <h2>Role-protected administration</h2>
        <p>Staff administration workspace featuring student record CRUD, campus event management with Fetch API, and tamper-evident audit logging.</p>
      </article>
    </section>

    <div class="landing-disclaimer">
      <strong>Coursework Prototype Notice:</strong> StudentHub has been developed as a complete university portal demonstration encompassing Practicals 1 through 14.
    </div>
  </main>

  <footer class="landing-footer">
    <div>
      <strong>StudentHub</strong> · University Academic & Administration Portal
    </div>
    <div style="display:flex;gap:16px;">
      <a href="pages/about.php">About</a>
      <a href="pages/faq.php">Frequently Asked Questions</a>
      <a href="pages/contact.php">Contact & Support</a>
      <a href="database/test_connection.php">System Status</a>
    </div>
  </footer>

  <script>
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

