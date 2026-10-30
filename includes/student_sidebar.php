<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$activeRoute = $activeRoute ?? 'overview';
$student = $_SESSION['student'] ?? [];
$studentName = (string)($student['name'] ?? $student['full_name'] ?? 'Student');
$studentId = (string)($student['student_id'] ?? 'STUDENT');
$studentInitials = app_get_initials($studentName);
?>
<aside class="sidebar" id="sidebar" aria-label="Main student navigation">
  <a class="brand" href="student_dashboard.php" aria-label="StudentHub home">
    <span class="brand-mark">S</span>
    <div class="brand-info">
      <span class="brand-name">Student<span>Hub</span></span>
      <span class="brand-sub">Academic portal</span>
    </div>
  </a>

  <!-- Collapse / Expand Trigger Button -->
  <button type="button" class="sidebar-collapse-toggle" id="sidebarToggleBtn" aria-label="Toggle sidebar collapse" data-tooltip="Toggle sidebar (Ctrl+B)">
    <span class="toggle-icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><polyline points="15 9 12 12 15 15"></polyline></svg>
    </span>
    <span class="toggle-text">Collapse sidebar</span>
    <kbd class="toggle-kbd">⌘B</kbd>
  </button>

  <div class="nav-heading">Workspace</div>
  <nav class="primary-nav">
    <a href="student_dashboard.php" class="nav-link <?= $activeRoute === 'overview' ? 'active' : '' ?>" data-tooltip="Overview">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></span>
      <span class="nav-text">Overview</span>
    </a>
    <a href="student_courses.php" class="nav-link <?= $activeRoute === 'courses' ? 'active' : '' ?>" data-tooltip="My courses">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></span>
      <span class="nav-text">My courses</span>
    </a>
    <a href="student_schedule.php" class="nav-link <?= $activeRoute === 'schedule' ? 'active' : '' ?>" data-tooltip="Schedule">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
      <span class="nav-text">Schedule</span>
    </a>
    <a href="student_assignments.php" class="nav-link <?= $activeRoute === 'assignments' ? 'active' : '' ?>" data-tooltip="Assignments (3)">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg></span>
      <span class="nav-text">Assignments</span>
      <span class="nav-badge" title="3 pending assignments">3</span>
    </a>
  </nav>

  <div class="nav-heading">Academic</div>
  <nav class="primary-nav">
    <a href="student_attendance.php" class="nav-link <?= $activeRoute === 'attendance' ? 'active' : '' ?>" data-tooltip="Attendance">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>
      <span class="nav-text">Attendance</span>
    </a>
    <a href="student_results.php" class="nav-link <?= $activeRoute === 'results' ? 'active' : '' ?>" data-tooltip="Results">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg></span>
      <span class="nav-text">Results</span>
    </a>
    <a href="student_events.php" class="nav-link <?= $activeRoute === 'events' ? 'active' : '' ?>" data-tooltip="Events">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span>
      <span class="nav-text">Events</span>
    </a>
    <a href="student_notices.php" class="nav-link <?= $activeRoute === 'notices' ? 'active' : '' ?>" data-tooltip="Notices">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg></span>
      <span class="nav-text">Notices</span>
    </a>
  </nav>

  <div class="nav-heading">Preferences & Support</div>
  <nav class="primary-nav">
    <a href="student_profile.php" class="nav-link <?= $activeRoute === 'profile' ? 'active' : '' ?>" data-tooltip="My profile">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
      <span class="nav-text">My profile</span>
    </a>
    <a href="student_help.php" class="nav-link <?= $activeRoute === 'help' ? 'active' : '' ?>" data-tooltip="Help & Support">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span>
      <span class="nav-text">Help & Support</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="term-card">
      <span class="term-dot"></span>
      <div>
        <strong>Fall semester</strong>
        <small>Academic year 2026–27</small>
      </div>
    </div>

    <a href="student_profile.php" class="sidebar-profile-card" data-tooltip="<?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') ?>)">
      <div class="avatar"><?= htmlspecialchars($studentInitials, ENT_QUOTES, 'UTF-8') ?></div>
      <div class="sidebar-profile-info">
        <strong><?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?></strong>
        <small><?= htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') ?></small>
      </div>
    </a>

    <form method="post" action="<?= app_base_url() ?>logout.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="btn-sidebar-logout" data-tooltip="Log out">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        <span class="nav-text">Log out</span>
      </button>
    </form>
  </div>
</aside>
