<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$student = $_SESSION['student'] ?? [];
$studentName = (string)($student['name'] ?? $student['full_name'] ?? 'Student');
$nameParts = preg_split('/\s+/', trim($studentName)) ?: ['Student'];
$initials = app_get_initials($studentName);

$pageTitle = 'Academic overview';
$pageCrumb = 'Overview';
$activeRoute = 'overview';
$portalType = 'student';

$noticesFile = dirname(__DIR__) . '/data/notices.json';
$campusNotices = [];
if (file_exists($noticesFile)) {
    $campusNotices = json_decode((string)file_get_contents($noticesFile), true) ?: [];
}

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <!-- The main workspace views render here via studyhub.js -->
    </main>

    <div class="mobile-scrim" id="mobileScrim"></div>
    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

  <script>
    window.APP_BASE_URL = <?= json_encode(app_base_url()) ?>;
    window.STUDENT_PROFILE = <?= json_encode($student, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.STUDENT_CSRF = <?= json_encode(app_csrf_token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.CAMPUS_NOTICES = <?= json_encode($campusNotices, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.addEventListener("pageshow", event => { if (event.persisted) window.location.reload(); });
  </script>
  <script src="<?= app_base_url() ?>js/studyhub.js?v=2.3"></script>
