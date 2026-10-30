<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';

$crumbCurrent = $pageCrumb ?? ($pageTitle ?? 'Overview');
$userSession = $_SESSION['student'] ?? [];
$userName = (string)($userSession['name'] ?? $userSession['full_name'] ?? 'User');
$userRole = (string)($userSession['role'] ?? 'Student');
$userInitials = app_get_initials($userName);
$profileUrl = $userRole === 'Admin' ? app_base_url() . 'admin/admin_settings.php' : app_base_url() . 'student/student_profile.php';
?>
<header class="topbar">
  <div class="topbar-left">
    <button class="mobile-toggle" id="menuToggle" type="button" aria-label="Toggle navigation menu" aria-expanded="false">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
    </button>
    <button class="topbar-sidebar-toggle" id="topbarSidebarToggle" type="button" aria-label="Toggle sidebar width" title="Toggle sidebar (Ctrl+B)">
      <svg class="icon-collapse" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><polyline points="15 9 12 12 15 15"></polyline></svg>
      <svg class="icon-expand" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><polyline points="12 9 15 12 12 15"></polyline></svg>
    </button>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <span class="crumb-parent"><?= $userRole === 'Admin' ? 'Administration' : 'Student workspace' ?></span>
      <span class="crumb-separator" aria-hidden="true">/</span>
      <span class="crumb-current" aria-current="page"><?= htmlspecialchars($crumbCurrent, ENT_QUOTES, 'UTF-8') ?></span>
    </nav>
  </div>

  <!-- CHARUSAT Official University Brand on Top of Every Screen -->
  <div class="topbar-center">
    <div class="charusat-topbar-brand" title="Charotar University of Science and Technology (CHARUSAT)">
      <img src="<?= app_base_url() ?>images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
    </div>
  </div>

  <div class="topbar-right">
    <div class="search-wrapper">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input id="pageSearch" class="search-input" type="search" placeholder="Search this page..." aria-label="Search page content">
      <kbd class="search-kbd">⌘K</kbd>
    </div>

    <!-- Theme Toggle -->
    <button id="themeToggleBtn" class="btn-icon-toggle" type="button" aria-label="Toggle dark mode" title="Switch between light and dark theme">
      <svg class="theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
      <svg class="theme-icon-moon" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
    </button>

    <?php if ($userRole === 'Student'): ?>
      <span class="demo-pill" title="This portal contains demo academic data"><span class="dot"></span>Demo data</span>
    <?php endif; ?>

    <a href="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>" class="topbar-avatar" title="<?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8') ?>)">
      <?= htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8') ?>
    </a>
  </div>
</header>


