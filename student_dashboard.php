<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
app_require_role('Student');
$student = $_SESSION['student'];
$nameParts = preg_split('/\s+/', trim((string)$student['name'])) ?: ['Student'];
$initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . (count($nameParts) > 1 ? substr((string)end($nameParts), 0, 1) : ''));
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#132238">
  <meta name="description" content="StudentHub — a clear workspace for courses, schedules, assignments, attendance and results.">
  <title>Overview · StudentHub</title>
  <link rel="stylesheet" href="css/studyhub.css">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
      <a class="brand" href="#overview" aria-label="StudentHub overview"><span class="brand-mark">S</span><span>Student<span class="brand-light">Hub</span><small>LEARNING PORTAL</small></span></a>
      <div class="nav-label">WORKSPACE</div>
      <nav class="primary-nav" id="primaryNav">
        <a href="#overview" data-route="overview"><span class="nav-icon">◫</span>Overview</a>
        <a href="#courses" data-route="courses"><span class="nav-icon">▤</span>My courses</a>
        <a href="#schedule" data-route="schedule"><span class="nav-icon">▦</span>Schedule</a>
        <a href="#assignments" data-route="assignments"><span class="nav-icon">✓</span>Assignments<span class="nav-count">3</span></a>
      </nav>
      <div class="nav-label nav-label-spaced">ACADEMIC</div>
      <nav class="primary-nav">
        <a href="#attendance" data-route="attendance"><span class="nav-icon">◷</span>Attendance</a>
        <a href="#results" data-route="results"><span class="nav-icon">▥</span>Results</a>
        <a href="#events" data-route="events"><span class="nav-icon">✳</span>Events</a>
        <a href="#notices" data-route="notices"><span class="nav-icon">♧</span>Notices</a>
      </nav>
      <div class="sidebar-bottom">
        <form class="sidebar-logout" method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>"><button type="submit"><span aria-hidden="true">↪</span> Log out</button></form>
        <div class="term-card"><span class="term-dot"></span><div><strong>Fall semester</strong><small>Academic year 2026–27</small></div></div>
        <a class="sidebar-profile" href="#profile" data-route="profile"><span class="avatar avatar-small"><?= app_escape($initials) ?></span><span><strong><?= app_escape((string)$student['name']) ?></strong><small><?= app_escape((string)$student['student_id']) ?></small></span><span class="profile-more">···</span></a>
      </div>
    </aside>

    <div class="workspace">
      <header class="topbar">
        <button class="mobile-menu" id="menuToggle" type="button" aria-label="Open navigation" aria-expanded="false">☰</button>
        <div class="breadcrumb"><span>Student workspace</span><span class="crumb-divider">/</span><strong id="pageCrumb">Overview</strong></div>
        <div class="topbar-actions">
          <label class="search-box"><span aria-hidden="true">⌕</span><input id="pageSearch" type="search" placeholder="Search this page" aria-label="Search this page"><kbd>⌘ K</kbd></label>
          <span class="sample-badge"><span></span>Demo academic data</span>
          <a class="avatar" href="#profile" data-route="profile" aria-label="Open profile"><?= app_escape($initials) ?></a>
        </div>
      </header>
      <main class="page-content" id="pageContent" tabindex="-1"></main>
      <footer class="page-footer"><span>StudentHub · Academic workspace</span><span>For demonstration and coursework</span></footer>
    </div>
  </div>
  <div class="mobile-scrim" id="mobileScrim"></div>
  <script>window.STUDENT_PROFILE = <?= json_encode($student, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>; window.STUDENT_CSRF = <?= json_encode(app_csrf_token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>; window.addEventListener("pageshow", event => { if (event.persisted) window.location.reload(); });</script>
  <script src="js/studyhub.js"></script>
</body>
</html>


