<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'Academic results';
$pageCrumb = 'Results';
$activeRoute = 'results';
$portalType = 'student';

$semesters = [
    [
        'sem' => 'Semester 3 (Current Odd Term 2026–27) · FTE CSPIT CSE Div-I',
        'sgpa' => '9.48',
        'credits' => 22,
        'grades' => [
            ['code' => 'CSUC201', 'name' => 'Fundamentals of Data Structure and Algorithm', 'credits' => 4, 'internal' => '94%', 'external' => '92%', 'grade' => 'A+', 'points' => 9.5],
            ['code' => 'ITUE204', 'name' => 'Web Development Frameworks', 'credits' => 5, 'internal' => '96%', 'external' => '98%', 'grade' => 'A+', 'points' => 10.0],
            ['code' => 'CEUE203', 'name' => 'Object Oriented Programming', 'credits' => 3, 'internal' => '88%', 'external' => '90%', 'grade' => 'A', 'points' => 9.0],
            ['code' => 'ITUC201', 'name' => 'Fundamentals of Computer Networks', 'credits' => 5, 'internal' => '90%', 'external' => '88%', 'grade' => 'A', 'points' => 9.0],
            ['code' => 'MSUD203', 'name' => 'Discrete Mathematics', 'credits' => 3, 'internal' => '92%', 'external' => '95%', 'grade' => 'A+', 'points' => 9.5],
            ['code' => 'HSUV201', 'name' => 'Creativity, Problem Solving and Innovation', 'credits' => 2, 'internal' => '98%', 'external' => '96%', 'grade' => 'A+', 'points' => 10.0]
        ]
    ],
    [
        'sem' => 'Semester 2 (Even Term 2025–26)',
        'sgpa' => '8.72',
        'credits' => 21,
        'grades' => [
            ['code' => 'CSUC102', 'name' => 'Programming for Problem Solving in C', 'credits' => 4, 'internal' => '90%', 'external' => '88%', 'grade' => 'A', 'points' => 9.0],
            ['code' => 'ECUC101', 'name' => 'Basic Electronics & Electrical Engineering', 'credits' => 4, 'internal' => '84%', 'external' => '82%', 'grade' => 'B+', 'points' => 8.0],
            ['code' => 'MSUD102', 'name' => 'Linear Algebra & Calculus', 'credits' => 4, 'internal' => '92%', 'external' => '95%', 'grade' => 'A+', 'points' => 9.5],
            ['code' => 'HSUV102', 'name' => 'Professional Communication Skills', 'credits' => 3, 'internal' => '88%', 'external' => '86%', 'grade' => 'A', 'points' => 9.0],
            ['code' => 'CSUC103', 'name' => 'IT Workshop & Linux Shell Scripting', 'credits' => 6, 'internal' => '96%', 'external' => '94%', 'grade' => 'A+', 'points' => 9.5]
        ]
    ]
];

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>Academic transcript & results</h1>
          <p>Semester grade sheets, cumulative grade point average, and credit audit.</p>
        </div>
        <div class="header-actions">
          <button type="button" class="btn btn-secondary btn-sm" data-action="print">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print official transcript
          </button>
        </div>
      </div>

      <div class="hero-card">
        <div class="hero-content">
          <h2>Cumulative GPA: 8.95 / 10.00</h2>
          <p>Standing: First Class with Distinction · 63 of 130 Total Degree Credits Completed (Semester 3 Current)</p>
        </div>
        <div class="hero-actions">
          <span class="badge badge-success" style="font-size:13px;padding:6px 14px;">Academic standing: Excellent</span>
        </div>
      </div>

      <div class="card-stack" style="margin-top:24px;">
        <?php foreach ($semesters as $s): ?>
          <section class="card">
            <div class="card-header">
              <div>
                <h2><?= htmlspecialchars($s['sem'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p>Term Credits: <?= $s['credits'] ?> · SGPA: <strong class="tabular-nums" style="color:var(--primary);"><?= $s['sgpa'] ?></strong></p>
              </div>
              <span class="badge badge-info">Verified record</span>
            </div>

            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th scope="col">Course</th>
                    <th scope="col" class="col-numeric">Credits</th>
                    <th scope="col" class="col-numeric">Internal assessment</th>
                    <th scope="col" class="col-numeric">End-semester exam</th>
                    <th scope="col">Letter grade</th>
                    <th scope="col" class="col-numeric">Grade point</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($s['grades'] as $g): ?>
                    <tr data-search="<?= htmlspecialchars($g['name'] . ' ' . $g['code'], ENT_QUOTES, 'UTF-8') ?>">
                      <td>
                        <strong><?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span class="badge badge-neutral" style="margin-left:6px;"><?= htmlspecialchars($g['code'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td class="col-numeric"><?= $g['credits'] ?></td>
                      <td class="col-numeric"><?= $g['internal'] ?></td>
                      <td class="col-numeric"><?= $g['external'] ?></td>
                      <td>
                        <span class="badge <?= in_array($g['grade'], ['A+', 'A', 'A−'], true) ? 'badge-success' : 'badge-info' ?>">
                          <?= htmlspecialchars($g['grade'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                      </td>
                      <td class="col-numeric"><strong><?= number_format((float)$g['points'], 1) ?></strong></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </section>
        <?php endforeach; ?>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>


