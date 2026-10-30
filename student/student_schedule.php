<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'Class schedule';
$pageCrumb = 'Schedule';
$activeRoute = 'schedule';
$portalType = 'student';

// CHARUSAT CSPIT Department of CSE - B.Tech (CSE) Semester 3 (Odd Term) Division-I Official Timetable
$weekdaySchedules = [
    1 => [
        [
            'time' => '09:10 AM - 10:10 AM',
            'course' => 'Fundamentals of Data Structure and Algorithm',
            'code' => 'CSUC201',
            'abbr' => 'FDSA(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Vidisha Pradhan (VMP)',
            'type' => 'Theory'
        ],
        [
            'time' => '10:10 AM - 11:10 AM',
            'course' => 'Fundamentals of Computer Networks',
            'code' => 'ITUC201',
            'abbr' => 'FCN(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Harshul Yagnik (HYY)',
            'type' => 'Theory'
        ],
        [
            'time' => '11:10 AM - 12:10 PM',
            'course' => 'Lunch Break',
            'code' => 'BREAK',
            'abbr' => 'LUNCH',
            'room' => 'Campus Dining / Student Center',
            'faculty' => '1 Hour Recess',
            'type' => 'Break'
        ],
        [
            'time' => '12:10 PM - 02:10 PM',
            'course' => 'Creativity, Problem Solving and Innovation',
            'code' => 'HSUV201',
            'abbr' => 'HS(T/P)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Akshita Kadam (ADK)',
            'type' => 'Theory / Practical'
        ],
        [
            'time' => '02:10 PM - 02:20 PM',
            'course' => 'Short Recess',
            'code' => 'BREAK',
            'abbr' => 'RECESS',
            'room' => 'Department Concourse',
            'faculty' => '10 Min Break',
            'type' => 'Break'
        ],
        [
            'time' => '02:20 PM - 04:20 PM',
            'course' => 'Laboratory Practical Sessions (Batch-wise)',
            'code' => 'LAB-P1',
            'abbr' => 'LAB',
            'room' => 'Labs 632 / 638 / 633',
            'faculty' => 'Department Faculty Team',
            'type' => 'Laboratory Practical',
            'batches' => [
                ['batch' => 'A1', 'course' => 'CSUC201 · FDSA(P)', 'room' => 'Lab 632', 'faculty' => 'Prof. Avani Khokhariya (AVK)'],
                ['batch' => 'B1', 'course' => 'ITUC201 · FCN(P)', 'room' => 'Lab 638', 'faculty' => 'Prof. Brinda Patel (BPP)'],
                ['batch' => 'C1', 'course' => 'ITUE204 · WDF(P)', 'room' => 'Lab 633', 'faculty' => 'Prof. Akshita Kadam (ADK)']
            ]
        ]
    ],
    2 => [
        [
            'time' => '09:10 AM - 10:10 AM',
            'course' => 'Discrete Mathematics',
            'code' => 'MSUD203',
            'abbr' => 'MATHS(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Pragnesh Makwana (PM)',
            'type' => 'Theory'
        ],
        [
            'time' => '10:10 AM - 11:10 AM',
            'course' => 'Fundamentals of Data Structure and Algorithm',
            'code' => 'CSUC201',
            'abbr' => 'FDSA(T)',
            'room' => 'Room 506',
            'faculty' => 'Dr. Amit Thakkar (ART, HOD)',
            'type' => 'Theory'
        ],
        [
            'time' => '11:10 AM - 12:10 PM',
            'course' => 'Lunch Break',
            'code' => 'BREAK',
            'abbr' => 'LUNCH',
            'room' => 'Campus Dining / Student Center',
            'faculty' => '1 Hour Recess',
            'type' => 'Break'
        ],
        [
            'time' => '12:10 PM - 02:10 PM',
            'course' => 'Laboratory Practical Sessions (Batch-wise)',
            'code' => 'LAB-P2',
            'abbr' => 'LAB',
            'room' => 'Labs 633 / 632 / 631',
            'faculty' => 'Department Faculty Team',
            'type' => 'Laboratory Practical',
            'batches' => [
                ['batch' => 'A1', 'course' => 'ITUC201 · FCN(P)', 'room' => 'Lab 633', 'faculty' => 'Prof. Brinda Patel (BPP)'],
                ['batch' => 'B1', 'course' => 'CEUE203 · OOP(P)', 'room' => 'Lab 632', 'faculty' => 'Prof. Akshita Kadam (ADK)'],
                ['batch' => 'C1', 'course' => 'ITUC201 · FCN(P)', 'room' => 'Lab 631', 'faculty' => 'Prof. Harshul Yagnik (HYY)']
            ]
        ],
        [
            'time' => '02:10 PM - 02:20 PM',
            'course' => 'Short Recess',
            'code' => 'BREAK',
            'abbr' => 'RECESS',
            'room' => 'Department Concourse',
            'faculty' => '10 Min Break',
            'type' => 'Break'
        ],
        [
            'time' => '02:20 PM - 03:20 PM',
            'course' => 'Fundamentals of Computer Networks',
            'code' => 'ITUC201',
            'abbr' => 'FCN(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Harshul Yagnik (HYY)',
            'type' => 'Theory'
        ],
        [
            'time' => '03:20 PM - 04:20 PM',
            'course' => 'Web Development Frameworks',
            'code' => 'ITUE204',
            'abbr' => 'WDF(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Srushti Gajjar (SRG)',
            'type' => 'Theory'
        ]
    ],
    3 => [
        [
            'time' => '09:10 AM - 10:10 AM',
            'course' => 'Discrete Mathematics',
            'code' => 'MSUD203',
            'abbr' => 'MATHS(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Pragnesh Makwana (PM)',
            'type' => 'Theory'
        ],
        [
            'time' => '10:10 AM - 11:10 AM',
            'course' => 'Object Oriented Programming',
            'code' => 'CEUE203',
            'abbr' => 'OOP(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Siddharth Shah (SSS)',
            'type' => 'Theory'
        ],
        [
            'time' => '11:10 AM - 12:10 PM',
            'course' => 'Lunch Break',
            'code' => 'BREAK',
            'abbr' => 'LUNCH',
            'room' => 'Campus Dining / Student Center',
            'faculty' => '1 Hour Recess',
            'type' => 'Break'
        ],
        [
            'time' => '12:10 PM - 02:10 PM',
            'course' => 'Laboratory Practical Sessions (Batch-wise)',
            'code' => 'LAB-P3',
            'abbr' => 'LAB',
            'room' => 'Labs 633 / 632 / 634',
            'faculty' => 'Department Faculty Team',
            'type' => 'Laboratory Practical',
            'batches' => [
                ['batch' => 'A1', 'course' => 'ITUE204 · WDF(P)', 'room' => 'Lab 633', 'faculty' => 'Prof. Dhara Solanki (DSS)'],
                ['batch' => 'B1', 'course' => 'ITUE204 · WDF(P)', 'room' => 'Lab 632', 'faculty' => 'Prof. Brinda Patel (BPP)'],
                ['batch' => 'C1', 'course' => 'CSUC201 · FDSA(P)', 'room' => 'Lab 634', 'faculty' => 'Prof. Avani Khokhariya (AVK)']
            ]
        ],
        [
            'time' => '02:10 PM - 02:20 PM',
            'course' => 'Short Recess',
            'code' => 'BREAK',
            'abbr' => 'RECESS',
            'room' => 'Department Concourse',
            'faculty' => '10 Min Break',
            'type' => 'Break'
        ],
        [
            'time' => '02:20 PM - 03:20 PM',
            'course' => 'Web Development Frameworks',
            'code' => 'ITUE204',
            'abbr' => 'WDF(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Srushti Gajjar (SRG)',
            'type' => 'Theory'
        ],
        [
            'time' => '03:20 PM - 04:20 PM',
            'course' => 'Fundamentals of Data Structure and Algorithm',
            'code' => 'CSUC201',
            'abbr' => 'FDSA(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Vidisha Pradhan (VMP)',
            'type' => 'Theory'
        ]
    ],
    4 => [
        [
            'time' => '09:10 AM - 10:10 AM',
            'course' => 'Web Development Frameworks',
            'code' => 'ITUE204',
            'abbr' => 'WDF(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Vaibhavi Patel (VYP)',
            'type' => 'Theory'
        ],
        [
            'time' => '10:10 AM - 11:10 AM',
            'course' => 'Fundamentals of Computer Networks',
            'code' => 'ITUC201',
            'abbr' => 'FCN(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Harshul Yagnik (HYY)',
            'type' => 'Theory'
        ],
        [
            'time' => '11:10 AM - 12:10 PM',
            'course' => 'Lunch Break',
            'code' => 'BREAK',
            'abbr' => 'LUNCH',
            'room' => 'Campus Dining / Student Center',
            'faculty' => '1 Hour Recess',
            'type' => 'Break'
        ],
        [
            'time' => '12:10 PM - 02:10 PM',
            'course' => 'Laboratory Practical Sessions (Batch-wise)',
            'code' => 'LAB-P4A',
            'abbr' => 'LAB',
            'room' => 'Labs 631 / 638 / 632',
            'faculty' => 'Department Faculty Team',
            'type' => 'Laboratory Practical',
            'batches' => [
                ['batch' => 'A1', 'course' => 'ITUE204 · WDF(P)', 'room' => 'Lab 631', 'faculty' => 'Prof. Vaibhavi Patel (VYP)'],
                ['batch' => 'B1', 'course' => 'CSUC201 · FDSA(P)', 'room' => 'Lab 638', 'faculty' => 'Prof. Avani Khokhariya (AVK)'],
                ['batch' => 'C1', 'course' => 'ITUE204 · WDF(P)', 'room' => 'Lab 632', 'faculty' => 'Prof. Dhara Solanki (DSS)']
            ]
        ],
        [
            'time' => '02:10 PM - 02:20 PM',
            'course' => 'Short Recess',
            'code' => 'BREAK',
            'abbr' => 'RECESS',
            'room' => 'Department Concourse',
            'faculty' => '10 Min Break',
            'type' => 'Break'
        ],
        [
            'time' => '02:20 PM - 04:20 PM',
            'course' => 'Laboratory Practical Sessions (Batch-wise)',
            'code' => 'LAB-P4B',
            'abbr' => 'LAB',
            'room' => 'Labs 638 / 632 / 631',
            'faculty' => 'Department Faculty Team',
            'type' => 'Laboratory Practical',
            'batches' => [
                ['batch' => 'A1', 'course' => 'CEUE203 · OOP(P)', 'room' => 'Lab 638', 'faculty' => 'Prof. Akshita Kadam (ADK)'],
                ['batch' => 'B1', 'course' => 'ITUE204 · WDF(P)', 'room' => 'Lab 632', 'faculty' => 'Prof. Brinda Patel (BPP)'],
                ['batch' => 'C1', 'course' => 'CEUE203 · OOP(P)', 'room' => 'Lab 631', 'faculty' => 'Prof. Siddharth Shah (SSS)']
            ]
        ]
    ],
    5 => [
        [
            'time' => '09:10 AM - 10:10 AM',
            'course' => 'Discrete Mathematics',
            'code' => 'MSUD203',
            'abbr' => 'MATHS(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Pragnesh Makwana (PM)',
            'type' => 'Theory'
        ],
        [
            'time' => '10:10 AM - 11:10 AM',
            'course' => 'Fundamentals of Data Structure and Algorithm',
            'code' => 'CSUC201',
            'abbr' => 'FDSA(T)',
            'room' => 'Room 506',
            'faculty' => 'Dr. Amit Thakkar (ART, HOD)',
            'type' => 'Theory'
        ],
        [
            'time' => '11:10 AM - 12:10 PM',
            'course' => 'Lunch Break',
            'code' => 'BREAK',
            'abbr' => 'LUNCH',
            'room' => 'Campus Dining / Student Center',
            'faculty' => '1 Hour Recess',
            'type' => 'Break'
        ],
        [
            'time' => '12:10 PM - 02:10 PM',
            'course' => 'Department Counselling & Mentorship Sessions',
            'code' => 'COUNSEL',
            'abbr' => 'COUNSELLING',
            'room' => 'Rooms CS-631 / CS-632 / CS-633',
            'faculty' => 'Department Faculty Mentors',
            'type' => 'Counselling',
            'batches' => [
                ['batch' => 'A1', 'course' => 'Mentorship / Advising', 'room' => 'Room CS-631', 'faculty' => 'Prof. Srushti Gajjar (SRG)'],
                ['batch' => 'B1', 'course' => 'Mentorship / Advising', 'room' => 'Room CS-632', 'faculty' => 'Prof. Vaibhavi Patel (VYP)'],
                ['batch' => 'C1', 'course' => 'Mentorship / Advising', 'room' => 'Room CS-633', 'faculty' => 'Prof. Akshita Kadam (ADK)']
            ]
        ],
        [
            'time' => '02:10 PM - 02:20 PM',
            'course' => 'Short Recess',
            'code' => 'BREAK',
            'abbr' => 'RECESS',
            'room' => 'Department Concourse',
            'faculty' => '10 Min Break',
            'type' => 'Break'
        ],
        [
            'time' => '02:20 PM - 03:20 PM',
            'course' => 'Fundamentals of Computer Networks',
            'code' => 'ITUC201',
            'abbr' => 'FCN(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Harshul Yagnik (HYY)',
            'type' => 'Theory'
        ],
        [
            'time' => '03:20 PM - 04:20 PM',
            'course' => 'Object Oriented Programming',
            'code' => 'CEUE203',
            'abbr' => 'OOP(T)',
            'room' => 'Room 506',
            'faculty' => 'Prof. Siddharth Shah (SSS)',
            'type' => 'Theory'
        ]
    ]
];

$viewMode = isset($_GET['view']) && in_array($_GET['view'], ['matrix', 'daily'], true) ? $_GET['view'] : 'matrix';
$selectedDay = isset($_GET['day']) ? (int)$_GET['day'] : (int)date('N');
if ($selectedDay < 1 || $selectedDay > 5) {
    $selectedDay = 1;
}

$dayNames = [
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday'
];

$todayClasses = $weekdaySchedules[$selectedDay] ?? [];

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>CHARUSAT Department of CSE Timetable</h1>
          <p>Academic Year 2026–27 · Semester 3 (Odd Term) · B.Tech Computer Science & Engineering (Division - I) · CSPIT · Room 506</p>
        </div>
        <div class="header-actions">
          <button type="button" class="btn btn-secondary btn-sm" data-action="print">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print timetable
          </button>
        </div>
      </div>

      <!-- Institutional Information Strip -->
      <div class="card" style="padding:16px 20px;margin-bottom:20px;background:var(--surface);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;border-left:4px solid var(--primary);">
        <div style="display:flex;align-items:center;gap:16px;">
          <img src="<?= app_base_url() ?>images/charusat-logo.png" alt="CHARUSAT Logo" style="height:44px;width:auto;object-fit:contain;">
          <div>
            <strong style="font-size:15px;color:var(--text);display:block;">Charotar University of Science and Technology (CHARUSAT)</strong>
            <small style="color:var(--text-muted);font-size:12px;">Semester 3 (Odd Term) · B.Tech Computer Science & Engineering (Division - I)</small>
          </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <span class="badge badge-info">Semester 3 (Odd Term)</span>
          <span class="badge badge-neutral">Division - I</span>
          <span class="badge badge-success">Classroom: Room 506</span>
          <span class="badge badge-neutral">W.E.F. 06/07/2026</span>
        </div>
      </div>

      <!-- View Switcher Segmented Control (Matrix vs Daily) -->
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div class="segmented-control" role="tablist" style="margin-bottom:0;">
          <button type="button" class="segmented-btn <?= $viewMode === 'matrix' ? 'active' : '' ?>" id="btnTabMatrix" onclick="switchTimetableTab('matrix')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
            Weekly Timetable Matrix
          </button>
          <button type="button" class="segmented-btn <?= $viewMode === 'daily' ? 'active' : '' ?>" id="btnTabDaily" onclick="switchTimetableTab('daily')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Daily Day-by-Day Agenda
          </button>
        </div>

        <div style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:14px;">
          <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#2563EB;border-radius:2px;"></span> Theory</span>
          <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#D97706;border-radius:2px;"></span> Lab Practical</span>
          <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#0D9488;border-radius:2px;"></span> Counselling</span>
        </div>
      </div>

      <!-- =========================================================
           TAB 1: OFFICIAL CHARUSAT WEEKLY TIMETABLE MATRIX
           ========================================================= -->
      <section id="panelMatrix" class="timetable-wrapper" style="<?= $viewMode === 'matrix' ? 'display:block;' : 'display:none;' ?>">
        <table class="timetable-grid-table">
          <thead>
            <tr>
              <th scope="col" style="width:110px;">Day / Time</th>
              <th scope="col" class="th-time">
                <span>09:10 – 10:10</span>
                <small>10:10 AM</small>
              </th>
              <th scope="col" class="th-time">
                <span>10:10 – 11:10</span>
                <small>11:10 AM</small>
              </th>
              <th scope="col" class="th-time" style="width:44px;background:var(--surface);">
                <span>11:10 – 12:10</span>
                <small>Lunch Break</small>
              </th>
              <th scope="col" class="th-time">
                <span>12:10 – 01:10</span>
                <small>01:10 PM</small>
              </th>
              <th scope="col" class="th-time">
                <span>01:10 – 02:10</span>
                <small>02:10 PM</small>
              </th>
              <th scope="col" class="th-time" style="width:36px;background:var(--surface);">
                <span>02:10 – 02:20</span>
                <small>Recess</small>
              </th>
              <th scope="col" class="th-time">
                <span>02:20 – 03:20</span>
                <small>03:20 PM</small>
              </th>
              <th scope="col" class="th-time">
                <span>03:20 – 04:20</span>
                <small>04:20 PM</small>
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- MONDAY -->
            <tr>
              <td class="td-day">
                <strong>Monday</strong>
                <span class="day-sub">Div-I · 506</span>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>CSUC201</span><span class="tt-abbr">FDSA(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>VMP</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUC201</span><span class="tt-abbr">FCN(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>HYY</strong></div>
                </div>
              </td>
              <!-- LUNCH BREAK SPANS MONDAY TO FRIDAY (5 ROWS) -->
              <td class="td-break" rowspan="5">LUNCH&nbsp;BREAK</td>
              <td colspan="2">
                <div class="tt-block theory" style="min-height:58px;">
                  <div class="tt-code"><span>HSUV201</span><span class="tt-abbr">HS(T/P)</span></div>
                  <div class="tt-meta"><span>📍 Room 506</span><strong>ADK · Prof. Akshita Kadam</strong></div>
                </div>
              </td>
              <!-- SHORT BREAK SPANS MONDAY TO FRIDAY (5 ROWS) -->
              <td class="td-break short" rowspan="5">RECESS</td>
              <td colspan="2">
                <div class="tt-block lab" style="min-height:74px;">
                  <div class="tt-code" style="color:#D97706;margin-bottom:2px;">
                    <span>LABORATORY SESSIONS</span>
                    <span class="tt-abbr" style="background:#FDE68A;color:#92400E;">PRACTICAL</span>
                  </div>
                  <div class="tt-lab-batch"><span class="batch-badge a1" style="width:22px;height:18px;font-size:10px;">A1</span> CSUC201 · FDSA(P) · Lab 632 · <strong>AVK</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge b1" style="width:22px;height:18px;font-size:10px;">B1</span> ITUC201 · FCN(P) · Lab 638 · <strong>BPP</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge c1" style="width:22px;height:18px;font-size:10px;">C1</span> ITUE204 · WDF(P) · Lab 633 · <strong>ADK</strong></div>
                </div>
              </td>
            </tr>

            <!-- TUESDAY -->
            <tr>
              <td class="td-day">
                <strong>Tuesday</strong>
                <span class="day-sub">Div-I · 506</span>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>MSUD203</span><span class="tt-abbr">MATHS(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>PM</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>CSUC201</span><span class="tt-abbr">FDSA(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>ART</strong></div>
                </div>
              </td>
              <td colspan="2">
                <div class="tt-block lab" style="min-height:74px;">
                  <div class="tt-code" style="color:#D97706;margin-bottom:2px;">
                    <span>LABORATORY SESSIONS</span>
                    <span class="tt-abbr" style="background:#FDE68A;color:#92400E;">PRACTICAL</span>
                  </div>
                  <div class="tt-lab-batch"><span class="batch-badge a1" style="width:22px;height:18px;font-size:10px;">A1</span> ITUC201 · FCN(P) · Lab 633 · <strong>BPP</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge b1" style="width:22px;height:18px;font-size:10px;">B1</span> CEUE203 · OOP(P) · Lab 632 · <strong>ADK</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge c1" style="width:22px;height:18px;font-size:10px;">C1</span> ITUC201 · FCN(P) · Lab 631 · <strong>HYY</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUC201</span><span class="tt-abbr">FCN(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>HYY</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUE204</span><span class="tt-abbr">WDF(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>SRG</strong></div>
                </div>
              </td>
            </tr>

            <!-- WEDNESDAY -->
            <tr>
              <td class="td-day">
                <strong>Wednesday</strong>
                <span class="day-sub">Div-I · 506</span>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>MSUD203</span><span class="tt-abbr">MATHS(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>PM</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>CEUE203</span><span class="tt-abbr">OOP(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>SSS</strong></div>
                </div>
              </td>
              <td colspan="2">
                <div class="tt-block lab" style="min-height:74px;">
                  <div class="tt-code" style="color:#D97706;margin-bottom:2px;">
                    <span>LABORATORY SESSIONS</span>
                    <span class="tt-abbr" style="background:#FDE68A;color:#92400E;">PRACTICAL</span>
                  </div>
                  <div class="tt-lab-batch"><span class="batch-badge a1" style="width:22px;height:18px;font-size:10px;">A1</span> ITUE204 · WDF(P) · Lab 633 · <strong>DSS</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge b1" style="width:22px;height:18px;font-size:10px;">B1</span> ITUE204 · WDF(P) · Lab 632 · <strong>BPP</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge c1" style="width:22px;height:18px;font-size:10px;">C1</span> CSUC201 · FDSA(P) · Lab 634 · <strong>AVK</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUE204</span><span class="tt-abbr">WDF(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>SRG</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>CSUC201</span><span class="tt-abbr">FDSA(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>VMP</strong></div>
                </div>
              </td>
            </tr>

            <!-- THURSDAY -->
            <tr>
              <td class="td-day">
                <strong>Thursday</strong>
                <span class="day-sub">Div-I · 506</span>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUE204</span><span class="tt-abbr">WDF(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>VYP</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUC201</span><span class="tt-abbr">FCN(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>HYY</strong></div>
                </div>
              </td>
              <td colspan="2">
                <div class="tt-block lab" style="min-height:74px;">
                  <div class="tt-code" style="color:#D97706;margin-bottom:2px;">
                    <span>LABORATORY SESSIONS 1</span>
                    <span class="tt-abbr" style="background:#FDE68A;color:#92400E;">PRACTICAL</span>
                  </div>
                  <div class="tt-lab-batch"><span class="batch-badge a1" style="width:22px;height:18px;font-size:10px;">A1</span> ITUE204 · WDF(P) · Lab 631 · <strong>VYP</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge b1" style="width:22px;height:18px;font-size:10px;">B1</span> CSUC201 · FDSA(P) · Lab 638 · <strong>AVK</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge c1" style="width:22px;height:18px;font-size:10px;">C1</span> ITUE204 · WDF(P) · Lab 632 · <strong>DSS</strong></div>
                </div>
              </td>
              <td colspan="2">
                <div class="tt-block lab" style="min-height:74px;">
                  <div class="tt-code" style="color:#D97706;margin-bottom:2px;">
                    <span>LABORATORY SESSIONS 2</span>
                    <span class="tt-abbr" style="background:#FDE68A;color:#92400E;">PRACTICAL</span>
                  </div>
                  <div class="tt-lab-batch"><span class="batch-badge a1" style="width:22px;height:18px;font-size:10px;">A1</span> CEUE203 · OOP(P) · Lab 638 · <strong>ADK</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge b1" style="width:22px;height:18px;font-size:10px;">B1</span> ITUE204 · WDF(P) · Lab 632 · <strong>BPP</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge c1" style="width:22px;height:18px;font-size:10px;">C1</span> CEUE203 · OOP(P) · Lab 631 · <strong>SSS</strong></div>
                </div>
              </td>
            </tr>

            <!-- FRIDAY -->
            <tr>
              <td class="td-day">
                <strong>Friday</strong>
                <span class="day-sub">Div-I · 506</span>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>MSUD203</span><span class="tt-abbr">MATHS(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>PM</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>CSUC201</span><span class="tt-abbr">FDSA(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>ART</strong></div>
                </div>
              </td>
              <td colspan="2">
                <div class="tt-block counsel" style="min-height:74px;">
                  <div class="tt-code" style="color:#0D9488;margin-bottom:2px;">
                    <span>COUNSELLING SESSIONS</span>
                    <span class="tt-abbr" style="background:#CCFBF1;color:#0F766E;">MENTORSHIP</span>
                  </div>
                  <div class="tt-lab-batch"><span class="batch-badge a1" style="width:22px;height:18px;font-size:10px;">A1</span> CS-631 · <strong>SRG · Prof. Srushti Gajjar</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge b1" style="width:22px;height:18px;font-size:10px;">B1</span> CS-632 · <strong>VYP · Prof. Vaibhavi Patel</strong></div>
                  <div class="tt-lab-batch"><span class="batch-badge c1" style="width:22px;height:18px;font-size:10px;">C1</span> CS-633 · <strong>ADK · Prof. Akshita Kadam</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>ITUC201</span><span class="tt-abbr">FCN(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>HYY</strong></div>
                </div>
              </td>
              <td>
                <div class="tt-block theory">
                  <div class="tt-code"><span>CEUE203</span><span class="tt-abbr">OOP(T)</span></div>
                  <div class="tt-meta"><span>📍 506</span><strong>SSS</strong></div>
                </div>
              </td>
            </tr>

            <!-- SATURDAY -->
            <tr>
              <td class="td-day">
                <strong>Saturday</strong>
                <span class="day-sub">Co-curricular</span>
              </td>
              <td colspan="8" class="td-free">
                <span>Self-Study, Library Research, Remedial Laboratory Practice & Co-curricular Technical Activities</span>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- =========================================================
           TAB 2: DAILY DAY-BY-DAY AGENDA (INTERACTIVE TIMELINE)
           ========================================================= -->
      <section id="panelDaily" style="<?= $viewMode === 'daily' ? 'display:block;' : 'display:none;' ?>">
        <!-- Day Selector Tabs -->
        <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
          <?php foreach ($dayNames as $dayNum => $dayName): ?>
            <button type="button" class="btn <?= $selectedDay === $dayNum ? 'btn-primary' : 'btn-secondary' ?> btn-day-tab" data-day="<?= $dayNum ?>" onclick="selectDailyTab(<?= $dayNum ?>)">
              <?= $dayName ?>
              <?php if ((int)date('N') === $dayNum): ?>
                <span class="badge badge-amber" style="margin-left:6px;font-size:10px;padding:2px 6px;">Today</span>
              <?php endif; ?>
            </button>
          <?php endforeach; ?>
        </div>

        <?php foreach ($weekdaySchedules as $dayNum => $classes): ?>
          <div class="daily-schedule-card card" id="dayCard-<?= $dayNum ?>" style="<?= $selectedDay === $dayNum ? 'display:block;' : 'display:none;' ?>padding:22px;">
            <div class="card-header" style="padding:0 0 16px 0;margin-bottom:18px;">
              <div>
                <h2 style="font-size:17px;margin:0 0 4px;"><?= $dayNames[$dayNum] ?> Schedule</h2>
                <p style="color:var(--text-muted);font-size:13px;margin:0;"><?= count($classes) ?> scheduled academic blocks · Room 506 & Specialized Laboratories</p>
              </div>
              <span class="badge badge-info">Semester 3 · Division I</span>
            </div>

            <div class="timeline-list">
              <?php foreach ($classes as $c): 
                $isBreak = $c['code'] === 'BREAK';
                $isLab = $c['type'] === 'Laboratory Practical';
                $isCounsel = $c['type'] === 'Counselling';
              ?>
                <div class="timeline-item" style="<?= $isBreak ? 'opacity:0.75;background:var(--border-subtle);border-style:dashed;' : '' ?>" data-search="<?= htmlspecialchars($c['course'] . ' ' . $c['code'] . ' ' . $c['faculty'] . ' ' . $c['room'], ENT_QUOTES, 'UTF-8') ?>">
                  <span class="timeline-time"><?= htmlspecialchars($c['time'], ENT_QUOTES, 'UTF-8') ?></span>

                  <div class="timeline-details" style="flex:1;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                      <?php if (!$isBreak): ?>
                        <span class="badge <?= $isLab ? 'badge-warning' : ($isCounsel ? 'badge-info' : 'badge-primary') ?>" style="font-size:11px;">
                          <?= htmlspecialchars($c['code'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                      <?php endif; ?>
                      <strong><?= htmlspecialchars($c['course'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <?php if (!empty($c['batches'])): ?>
                      <div class="batch-cards-container">
                        <?php foreach ($c['batches'] as $b): 
                          $batchClass = strtolower($b['batch']);
                        ?>
                          <div class="batch-card">
                            <span class="batch-badge <?= $batchClass ?>"><?= htmlspecialchars($b['batch'], ENT_QUOTES, 'UTF-8') ?></span>
                            <div class="batch-card-info">
                              <strong><?= htmlspecialchars($b['course'], ENT_QUOTES, 'UTF-8') ?></strong>
                              <small>📍 <?= htmlspecialchars($b['room'], ENT_QUOTES, 'UTF-8') ?> · 👤 <?= htmlspecialchars($b['faculty'], ENT_QUOTES, 'UTF-8') ?></small>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php else: ?>
                      <small style="color:var(--text-muted);display:block;line-height:1.5;">
                        📍 <?= htmlspecialchars($c['room'], ENT_QUOTES, 'UTF-8') ?> · 👤 <?= htmlspecialchars($c['faculty'], ENT_QUOTES, 'UTF-8') ?>
                      </small>
                    <?php endif; ?>
                  </div>

                  <div>
                    <span class="badge <?= $isBreak ? 'badge-neutral' : ($isLab ? 'badge-warning' : ($isCounsel ? 'badge-info' : 'badge-success')) ?>">
                      <?= htmlspecialchars($c['type'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </section>

      <!-- Department Faculty Contact & Course Key -->
      <section class="card" style="margin-top:24px;padding:22px;">
        <div class="card-header" style="padding:0 0 14px 0;margin-bottom:16px;">
          <div>
            <h2 style="font-size:16px;margin:0 0 4px;">Semester 3 Course & Faculty Directory</h2>
            <p style="color:var(--text-muted);font-size:13px;margin:0;">Official Teaching Scheme & Faculty Abbreviation Index</p>
          </div>
          <span class="badge badge-neutral">CSPIT CSE</span>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Code</th>
                <th>Course Name</th>
                <th>Abbr.</th>
                <th>Credits</th>
                <th>Instructors / Faculty Members</th>
                <th>Assigned Locations</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>CSUC201</strong></td>
                <td>Fundamentals of Data Structure and Algorithm</td>
                <td><span class="badge badge-info">FDSA</span></td>
                <td>3+1 (T+P) = 4</td>
                <td>Dr. Amit Thakkar (ART, HOD), Prof. Avani Khokhariya (AVK), Prof. Gayatree Parbat (GP), Prof. Vidisha Pradhan (VMP)</td>
                <td>Room 506, Labs 632 / 634 / 638</td>
              </tr>
              <tr>
                <td><strong>ITUE204</strong></td>
                <td>Web Development Frameworks</td>
                <td><span class="badge badge-info">WDF</span></td>
                <td>3+2 (T+P) = 5</td>
                <td>Prof. Srushti Gajjar (SRG), Prof. Vaibhavi Patel (VYP), Prof. Dhara Solanki (DSS), Prof. Brinda Patel (BPP), Prof. Akshita Kadam (ADK)</td>
                <td>Room 506, Labs 631 / 632 / 633</td>
              </tr>
              <tr>
                <td><strong>CEUE203</strong></td>
                <td>Object Oriented Programming</td>
                <td><span class="badge badge-info">OOP</span></td>
                <td>2+1 (T+P) = 3</td>
                <td>Prof. Siddharth Shah (SSS), Prof. Akshita Kadam (ADK)</td>
                <td>Room 506, Labs 631 / 632 / 638</td>
              </tr>
              <tr>
                <td><strong>ITUC201</strong></td>
                <td>Fundamentals of Computer Networks</td>
                <td><span class="badge badge-info">FCN</span></td>
                <td>4+1 (T+P) = 5</td>
                <td>Prof. Harshul Yagnik (HYY), Prof. Brinda Patel (BPP)</td>
                <td>Room 506, Labs 631 / 633 / 638</td>
              </tr>
              <tr>
                <td><strong>MSUD203</strong></td>
                <td>Discrete Mathematics</td>
                <td><span class="badge badge-info">MATHS</span></td>
                <td>3+0 (T+P) = 3</td>
                <td>Prof. Pragnesh Makwana (PM)</td>
                <td>Room 506</td>
              </tr>
              <tr>
                <td><strong>HSUV201</strong></td>
                <td>Creativity, Problem Solving and Innovation</td>
                <td><span class="badge badge-info">HS</span></td>
                <td>0+2 (T+P) = 2</td>
                <td>Prof. Akshita Kadam (ADK)</td>
                <td>Room 506</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Official Signatories Footer -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-top:20px;padding-top:16px;border-top:1px solid var(--border-subtle);text-align:center;">
          <div>
            <strong style="display:block;font-size:13px;color:var(--text);">Hemang A Thakar, Avani V Khokhariya</strong>
            <small style="color:var(--text-muted);font-size:12px;">Time Table Coordinators</small>
          </div>
          <div>
            <strong style="display:block;font-size:13px;color:var(--text);">Dr. Amit R Thakkar</strong>
            <small style="color:var(--text-muted);font-size:12px;">Head of Department (HOD, CSE)</small>
          </div>
          <div>
            <strong style="display:block;font-size:13px;color:var(--text);">Dr. Trushit K Upadhyaya</strong>
            <small style="color:var(--text-muted);font-size:12px;">Principal, CSPIT</small>
          </div>
          <div>
            <strong style="display:block;font-size:13px;color:var(--text);">Dr. Vijaykumar P Chaudhary</strong>
            <small style="color:var(--text-muted);font-size:12px;">Dean, FTE</small>
          </div>
        </div>
      </section>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
  </div>
</div>

<script>
  function switchTimetableTab(view) {
    const pMatrix = document.getElementById('panelMatrix');
    const pDaily = document.getElementById('panelDaily');
    const bMatrix = document.getElementById('btnTabMatrix');
    const bDaily = document.getElementById('btnTabDaily');

    if (view === 'matrix') {
      if (pMatrix) pMatrix.style.display = 'block';
      if (pDaily) pDaily.style.display = 'none';
      if (bMatrix) bMatrix.classList.add('active');
      if (bDaily) bDaily.classList.remove('active');
      history.replaceState(null, '', 'student_schedule.php?view=matrix');
    } else {
      if (pMatrix) pMatrix.style.display = 'none';
      if (pDaily) pDaily.style.display = 'block';
      if (bMatrix) bMatrix.classList.remove('active');
      if (bDaily) bDaily.classList.add('active');
      history.replaceState(null, '', 'student_schedule.php?view=daily');
    }
  }

  function selectDailyTab(dayNum) {
    document.querySelectorAll('.btn-day-tab').forEach(b => {
      const match = parseInt(b.dataset.day, 10) === dayNum;
      b.className = match ? 'btn btn-primary btn-day-tab' : 'btn btn-secondary btn-day-tab';
    });
    document.querySelectorAll('.daily-schedule-card').forEach(card => {
      card.style.display = card.id === 'dayCard-' + dayNum ? 'block' : 'none';
    });
  }
</script>

