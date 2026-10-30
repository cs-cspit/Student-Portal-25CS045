<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'Attendance register';
$pageCrumb = 'Attendance';
$activeRoute = 'attendance';
$portalType = 'student';

$coursesAttendance = [
    ['code' => 'CSUC201', 'name' => 'Fundamentals of Data Structure and Algorithm', 'held' => 36, 'attended' => 34, 'percentage' => 94, 'faculty' => 'Dr. Amit Thakkar / Prof. Avani Khokhariya'],
    ['code' => 'ITUE204', 'name' => 'Web Development Frameworks', 'held' => 40, 'attended' => 38, 'percentage' => 95, 'faculty' => 'Prof. Srushti Gajjar / Prof. Vaibhavi Patel'],
    ['code' => 'CEUE203', 'name' => 'Object Oriented Programming', 'held' => 32, 'attended' => 29, 'percentage' => 91, 'faculty' => 'Prof. Siddharth Shah / Prof. Akshita Kadam'],
    ['code' => 'ITUC201', 'name' => 'Fundamentals of Computer Networks', 'held' => 38, 'attended' => 35, 'percentage' => 92, 'faculty' => 'Prof. Harshul Yagnik / Prof. Brinda Patel'],
    ['code' => 'MSUD203', 'name' => 'Discrete Mathematics', 'held' => 30, 'attended' => 28, 'percentage' => 93, 'faculty' => 'Prof. Pragnesh Makwana'],
    ['code' => 'HSUV201', 'name' => 'Creativity, Problem Solving and Innovation', 'held' => 20, 'attended' => 20, 'percentage' => 100, 'faculty' => 'Prof. Akshita Kadam'],
];

$totalHeld = array_sum(array_column($coursesAttendance, 'held'));
$totalAttended = array_sum(array_column($coursesAttendance, 'attended'));
$overallPercentage = round(($totalAttended / $totalHeld) * 100, 1);

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>Attendance breakdown</h1>
          <p>Monitor your class presence and maintain university mandatory 75% attendance criteria.</p>
        </div>
        <div class="header-actions">
          <button type="button" class="btn btn-secondary btn-sm" data-action="print">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print attendance sheet
          </button>
        </div>
      </div>

      <!-- KPI Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-label">Overall attendance</span>
          <strong class="stat-value tabular-nums"><?= $overallPercentage ?>%</strong>
          <span class="stat-helper positive">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Well above 75% requirement
          </span>
        </div>

        <div class="stat-card">
          <span class="stat-label">Total classes attended</span>
          <strong class="stat-value tabular-nums"><?= $totalAttended ?> <small>/ <?= $totalHeld ?></small></strong>
          <span class="stat-helper">Across 6 enrolled courses</span>
        </div>

        <div class="stat-card">
          <span class="stat-label">Courses below 75%</span>
          <strong class="stat-value tabular-nums" style="color:var(--success);">0</strong>
          <span class="stat-helper positive">No debarment warnings</span>
        </div>

        <div class="stat-card">
          <span class="stat-label">Course compliance</span>
          <strong class="stat-value tabular-nums" style="color:var(--success);">6 of 6</strong>
          <span class="stat-helper positive">100% eligibility confirmed</span>
        </div>
      </div>

      <!-- Attendance Table -->
      <section class="card">
        <div class="card-header">
          <div>
            <h2>Course-wise attendance register</h2>
            <p>Semester 3 (Odd Term 2026–27) · CSPIT CSE Division I Room 506</p>
          </div>
          <span class="badge badge-info">Semester 3</span>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th scope="col">Course</th>
                <th scope="col">Instructor</th>
                <th scope="col" class="col-numeric">Sessions held</th>
                <th scope="col" class="col-numeric">Attended</th>
                <th scope="col" class="col-numeric">Percentage</th>
                <th scope="col">Standing</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($coursesAttendance as $item): ?>
                <tr data-search="<?= htmlspecialchars($item['name'] . ' ' . $item['code'] . ' ' . $item['faculty'], ENT_QUOTES, 'UTF-8') ?>">
                  <td>
                    <strong><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <span class="badge badge-neutral" style="margin-left:6px;"><?= htmlspecialchars($item['code'], ENT_QUOTES, 'UTF-8') ?></span>
                  </td>
                  <td><?= htmlspecialchars($item['faculty'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="col-numeric"><?= $item['held'] ?></td>
                  <td class="col-numeric"><?= $item['attended'] ?></td>
                  <td class="col-numeric">
                    <strong class="tabular-nums" style="<?= $item['percentage'] < 80 ? 'color:var(--warning);' : '' ?>"><?= $item['percentage'] ?>%</strong>
                  </td>
                  <td>
                    <?php if ($item['percentage'] < 80): ?>
                      <span class="badge badge-warning">
                        <span class="badge-dot"></span> Needs attention (78%)
                      </span>
                    <?php else: ?>
                      <span class="badge badge-success">
                        <span class="badge-dot"></span> Good standing
                      </span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <div class="alert alert-info" style="margin-top:24px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <div>
          <strong>CHARUSAT Attendance Regulation Notice:</strong> A minimum of 75% attendance in both classroom lectures and practical laboratory sessions is mandatory to be eligible for university end-semester examinations.
        </div>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>


