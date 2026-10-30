<?php
declare(strict_types=1);
$activeRoute = $activeRoute ?? 'overview';
?>
<nav class="mobile-bottom-nav" aria-label="Quick mobile navigation">
  <ul>
    <li>
      <a href="student_dashboard.php" class="mobile-bottom-link <?= $activeRoute === 'overview' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        <span>Overview</span>
      </a>
    </li>
    <li>
      <a href="student_courses.php" class="mobile-bottom-link <?= $activeRoute === 'courses' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        <span>Courses</span>
      </a>
    </li>
    <li>
      <a href="student_schedule.php" class="mobile-bottom-link <?= $activeRoute === 'schedule' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>Schedule</span>
      </a>
    </li>
    <li>
      <a href="student_assignments.php" class="mobile-bottom-link <?= $activeRoute === 'assignments' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
        <span>Tasks</span>
      </a>
    </li>
    <li>
      <button type="button" id="mobileMoreBtn" class="mobile-bottom-link" style="background:transparent;border:0;cursor:pointer;">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
        <span>More</span>
      </button>
    </li>
  </ul>
</nav>

