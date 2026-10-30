<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$activeRoute = $activeRoute ?? 'overview';
$admin = $_SESSION['student'] ?? [];
$adminName = (string)($admin['name'] ?? $admin['full_name'] ?? 'Administrator');
$adminEmail = (string)($admin['email'] ?? 'admin@studenthub.local');
$adminInitials = app_get_initials($adminName);
?>
<aside class="admin-rail" id="sidebar" aria-label="Main administration navigation">
  <a class="admin-brand" href="admin_dashboard.php" aria-label="StudentHub Administration">
    <span class="brand-mark">S</span>
    <div class="brand-info">
      <span class="brand-name">Student<span>Hub</span></span>
      <span class="brand-sub">Administration workspace</span>
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

  <div class="admin-nav-label">Overview & Management</div>
  <nav class="admin-nav">
    <a href="admin_dashboard.php" class="admin-nav-link <?= $activeRoute === 'overview' ? 'is-active' : '' ?>" data-tooltip="Overview">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></span>
      <span class="nav-text">Overview</span>
    </a>
    <a href="student_management.php" class="admin-nav-link <?= $activeRoute === 'students' ? 'is-active' : '' ?>" data-tooltip="Students">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span>
      <span class="nav-text">Students</span>
    </a>
    <a href="admin_events.php" class="admin-nav-link <?= $activeRoute === 'events' ? 'is-active' : '' ?>" data-tooltip="Events">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
      <span class="nav-text">Events</span>
    </a>
    <a href="admin_registrations.php" class="admin-nav-link <?= $activeRoute === 'registrations' ? 'is-active' : '' ?>" data-tooltip="Registrations">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></span>
      <span class="nav-text">Registrations</span>
    </a>
    <a href="admin_inquiries.php" class="admin-nav-link <?= $activeRoute === 'inquiries' ? 'is-active' : '' ?>" data-tooltip="Help & Inquiries">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></span>
      <span class="nav-text">Help & Inquiries</span>
      <?php
        $pendingTickets = count(array_filter(app_get_help_requests(), static fn($r) => ($r['status'] ?? 'open') === 'open'));
        if ($pendingTickets > 0):
      ?>
        <span class="nav-badge" style="background:#EF4444;color:#fff;" title="<?= $pendingTickets ?> open inquiries"><?= $pendingTickets ?></span>
      <?php endif; ?>
    </a>
  </nav>

  <div class="admin-nav-label">Security & Access</div>
  <nav class="admin-nav">
    <a href="admin_register.php" class="admin-nav-link <?= $activeRoute === 'admin_register' ? 'is-active' : '' ?>" data-tooltip="Add administrator">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg></span>
      <span class="nav-text">Add administrator</span>
    </a>
    <a href="admin_audit.php" class="admin-nav-link <?= $activeRoute === 'audit' ? 'is-active' : '' ?>" data-tooltip="Audit log">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg></span>
      <span class="nav-text">Audit log</span>
    </a>
    <a href="admin_notices.php" class="admin-nav-link <?= $activeRoute === 'notices' ? 'is-active' : '' ?>" data-tooltip="Notices">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg></span>
      <span class="nav-text">Notices</span>
    </a>
    <a href="admin_settings.php" class="admin-nav-link <?= $activeRoute === 'settings' ? 'is-active' : '' ?>" data-tooltip="Settings">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></span>
      <span class="nav-text">Settings</span>
    </a>
  </nav>

  <div class="admin-nav-label">Preview</div>
  <nav class="admin-nav">
    <a href="<?= app_base_url() ?>index.php" target="_blank" class="admin-nav-link" data-tooltip="Public home ↗">
      <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></span>
      <span class="nav-text">Public home ↗</span>
    </a>
  </nav>

  <div class="admin-rail-bottom">
    <a href="admin_settings.php" class="sidebar-profile-card" data-tooltip="<?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?> (Administrator)">
      <div class="avatar"><?= htmlspecialchars($adminInitials, ENT_QUOTES, 'UTF-8') ?></div>
      <div class="sidebar-profile-info">
        <strong><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></strong>
        <small>Administrator</small>
      </div>
    </a>

    <form method="post" action="<?= app_base_url() ?>logout.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="admin-logout" data-tooltip="Log out">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        <span class="nav-text">Log out</span>
      </button>
    </form>
  </div>
</aside>

