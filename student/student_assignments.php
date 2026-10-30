<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'Assignments & deliverables';
$pageCrumb = 'Assignments';
$activeRoute = 'assignments';
$portalType = 'student';

$assignments = [
    [
        'id' => 'a1',
        'title' => 'Binary Search Trees & AVL Balancing — Problem Set',
        'code' => 'CSUC201',
        'course' => 'Fundamentals of Data Structure and Algorithm',
        'faculty' => 'Dr. Amit Thakkar / Prof. Avani Khokhariya',
        'due' => '02 Nov 2026',
        'due_time' => '11:59 PM',
        'due_badge' => 'Due in 4 days',
        'status' => 'Pending',
        'urgent' => true,
        'month' => 'NOV',
        'day' => '02',
        'submission_type' => 'Code repository & PDF report',
        'description' => 'Implement recursive insert, delete, and balancing rotations on an AVL tree. Include empirical runtime benchmarks against unbalanced BSTs.'
    ],
    [
        'id' => 'a2',
        'title' => 'Responsive Single-Page Application (SPA) Milestone',
        'code' => 'ITUE204',
        'course' => 'Web Development Frameworks',
        'faculty' => 'Prof. Srushti Gajjar / Prof. Vaibhavi Patel',
        'due' => '04 Nov 2026',
        'due_time' => '11:59 PM',
        'due_badge' => 'Due in 6 days',
        'status' => 'Pending',
        'urgent' => true,
        'month' => 'NOV',
        'day' => '04',
        'submission_type' => 'GitHub repo & component documentation',
        'description' => 'Develop a modular web application with clean separation of concerns, modern tokenized CSS design system, and client-side reactive state.'
    ],
    [
        'id' => 'a3',
        'title' => 'Polymorphism & Virtual File Persistence Lab Worksheet',
        'code' => 'CEUE203',
        'course' => 'Object Oriented Programming',
        'faculty' => 'Prof. Siddharth Shah / Prof. Akshita Kadam',
        'due' => '06 Nov 2026',
        'due_time' => '05:00 PM',
        'due_badge' => 'Due in 8 days',
        'status' => 'Pending',
        'urgent' => false,
        'month' => 'NOV',
        'day' => '06',
        'submission_type' => 'C++ / Java source code & execution output',
        'description' => 'Implement an academic entity inheritance model demonstrating dynamic binding, abstract base classes, and serialized file stream persistence.'
    ],
    [
        'id' => 'a4',
        'title' => 'Subnetting, CIDR & Wireshark Packet Analysis',
        'code' => 'ITUC201',
        'course' => 'Fundamentals of Computer Networks',
        'faculty' => 'Prof. Harshul Yagnik / Prof. Brinda Patel',
        'due' => '28 Oct 2026',
        'due_time' => '11:59 PM',
        'due_badge' => 'Submitted',
        'status' => 'Submitted',
        'urgent' => false,
        'month' => 'OCT',
        'day' => '28',
        'submission_type' => 'Submitted online',
        'description' => 'Calculate variable length subnet masks (VLSM), route aggregation prefixes, and inspect captured TCP 3-way handshake packets.'
    ],
    [
        'id' => 'a5',
        'title' => 'Planar Graphs & Recurrence Relations Proofs',
        'code' => 'MSUD203',
        'course' => 'Discrete Mathematics',
        'faculty' => 'Prof. Pragnesh Makwana',
        'due' => '24 Oct 2026',
        'due_time' => '11:59 PM',
        'due_badge' => 'Graded · 94 / 100',
        'status' => 'Completed',
        'urgent' => false,
        'month' => 'OCT',
        'day' => '24',
        'submission_type' => 'Graded submission',
        'description' => 'Formal mathematical induction proofs for graph coloring bounds, Euler characteristic formula, and generating function solutions.'
    ],
    [
        'id' => 'a6',
        'title' => 'Design Thinking & Innovation Sprint Canvas',
        'code' => 'HSUV201',
        'course' => 'Creativity, Problem Solving and Innovation',
        'faculty' => 'Prof. Akshita Kadam',
        'due' => '20 Oct 2026',
        'due_time' => '11:59 PM',
        'due_badge' => 'Graded · 96 / 100',
        'status' => 'Completed',
        'urgent' => false,
        'month' => 'OCT',
        'day' => '20',
        'submission_type' => 'Prototype deck & evaluation canvas',
        'description' => 'Synthesize empathize-define-ideate stage insights into a viable campus resource-optimization concept.'
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
          <h1>Assignments & deliverables</h1>
          <p>Track your homework, lab worksheets, and project milestones across all enrolled courses.</p>
        </div>
        <div class="header-actions">
          <span class="badge badge-amber" style="padding:6px 14px;font-size:13px;font-weight:700;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span id="pendingBadgeText">3 Pending deliverables</span>
          </span>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total assigned</span>
            <div class="stat-icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
          </div>
          <strong class="stat-value tabular-nums">5</strong>
          <span class="stat-helper">Fall semester 2026–27</span>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">To do / Pending</span>
            <div class="stat-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
          </div>
          <strong class="stat-value tabular-nums" id="kpiPendingCount" style="color:var(--warning);">3</strong>
          <span class="stat-helper warning">Action required</span>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Completed & graded</span>
            <div class="stat-icon-wrap success">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
          </div>
          <strong class="stat-value tabular-nums" id="kpiCompletedCount" style="color:var(--success);">2</strong>
          <span class="stat-helper positive" id="kpiCompletedRate">40% completion rate</span>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Next deadline</span>
            <div class="stat-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
          </div>
          <strong class="stat-value" style="font-size:20px;margin-top:2px;color:var(--warning);">Due in 4 days</strong>
          <span class="stat-helper">CSUC201 · 02 Nov 2026</span>
        </div>
      </div>

      <!-- Coursework Progress Bar -->
      <div class="coursework-progress-card">
        <div class="coursework-progress-header">
          <span class="coursework-progress-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            Coursework progress overview
          </span>
          <span class="coursework-progress-metric" id="progressMetricText">2 of 5 deliverables completed (40%)</span>
        </div>
        <div class="progress-bar-track" style="height:8px;border-radius:var(--radius-pill);background:var(--border-subtle);overflow:hidden;">
          <div class="progress-bar-fill" id="progressBarFill" style="width:40%;height:100%;background:var(--primary);border-radius:var(--radius-pill);transition:width 0.3s ease;"></div>
        </div>
      </div>

      <!-- Main Two-Column Layout -->
      <div class="two-column-layout">
        <!-- Left Column: Assignment Items -->
        <div class="column-main">
          <!-- Toolbar with Segmented Filter Pills & Search -->
          <div class="assignment-toolbar">
            <div class="segmented-control" role="group" aria-label="Filter assignments">
              <button type="button" class="segmented-btn active" data-filter="all">
                All assignments
                <span class="segmented-count" id="countAll">5</span>
              </button>
              <button type="button" class="segmented-btn" data-filter="pending">
                To do
                <span class="segmented-count" id="countPending">3</span>
              </button>
              <button type="button" class="segmented-btn" data-filter="completed">
                Completed
                <span class="segmented-count" id="countCompleted">2</span>
              </button>
            </div>

            <div style="position:relative;flex:1;max-width:280px;min-width:200px;">
              <input type="text" id="assignmentSearchInput" placeholder="Filter by title, code..." class="form-control" style="padding-left:32px;font-size:13px;height:36px;border-radius:var(--radius-control);">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:10px;top:10px;color:var(--text-muted);pointer-events:none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
          </div>

          <!-- Assignment Cards List -->
          <div class="assignment-list" id="assignmentContainer">
            <?php foreach ($assignments as $a): 
              $isInitialCompleted = in_array($a['status'], ['Completed', 'Submitted'], true);
              $stateClass = $a['urgent'] ? 'state-urgent' : ($a['status'] === 'Completed' ? 'state-completed' : ($a['status'] === 'Submitted' ? 'state-submitted' : ''));
            ?>
              <article class="assignment-item <?= $stateClass ?>" data-assignment-id="<?= htmlspecialchars($a['id'], ENT_QUOTES, 'UTF-8') ?>" data-status="<?= $isInitialCompleted ? 'completed' : 'pending' ?>" data-search="<?= htmlspecialchars(strtolower($a['title'] . ' ' . $a['code'] . ' ' . $a['course'] . ' ' . $a['faculty']), ENT_QUOTES, 'UTF-8') ?>">
                <!-- Checkbox -->
                <div class="assignment-check-wrapper">
                  <input type="checkbox" class="assignment-checkbox" data-assignment-chk="<?= htmlspecialchars($a['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $isInitialCompleted ? 'checked' : '' ?> aria-label="Mark <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?> complete">
                </div>

                <!-- Main Content -->
                <div class="assignment-content">
                  <div class="assignment-header-row">
                    <span class="badge badge-info"><?= htmlspecialchars($a['code'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="assignment-course-name"><?= htmlspecialchars($a['course'], ENT_QUOTES, 'UTF-8') ?></span>
                  </div>

                  <h2 class="assignment-title">
                    <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                  </h2>

                  <p class="assignment-desc">
                    <?= htmlspecialchars($a['description'], ENT_QUOTES, 'UTF-8') ?>
                  </p>

                  <div class="assignment-meta-row">
                    <span class="assignment-meta-item">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                      Due <?= htmlspecialchars($a['due'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($a['due_time'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="assignment-meta-item">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                      <?= htmlspecialchars($a['submission_type'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="assignment-meta-item">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                      <?= htmlspecialchars($a['faculty'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </div>
                </div>

                <!-- Right Status & Badge -->
                <div class="assignment-status-col">
                  <span class="assignment-due-chip <?= $a['urgent'] ? 'urgent' : ($a['status'] === 'Completed' ? 'completed' : ($a['status'] === 'Submitted' ? 'submitted' : 'normal')) ?>">
                    <?php if ($a['urgent']): ?>
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <?php elseif ($a['status'] === 'Completed' || $a['status'] === 'Submitted'): ?>
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php endif; ?>
                    <span class="chip-text"><?= htmlspecialchars($a['due_badge'], ENT_QUOTES, 'UTF-8') ?></span>
                  </span>

                  <span class="assignment-deadline-text">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <?= htmlspecialchars($a['due'], ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="alert alert-info" style="margin-top:20px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <div>
              <strong>Sync notice:</strong> Deliverable completion checkmarks are saved in your local browser storage for personal progress tracking. Official evaluations and grading are recorded by faculty upon lab verification.
            </div>
          </div>
        </div>

        <!-- Right Column: Deadlines Timeline & Guidelines -->
        <aside class="column-side" style="display:flex;flex-direction:column;gap:20px;">
          <!-- Upcoming Deadlines Timeline Card -->
          <div class="card" style="padding:20px;">
            <div class="card-header" style="padding:0 0 14px 0;margin-bottom:14px;">
              <h2 style="font-size:15px;display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary)"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Upcoming timeline
              </h2>
              <span class="badge badge-neutral" style="font-size:11px;">Next 14 days</span>
            </div>

            <div class="deadline-timeline">
              <?php foreach ($assignments as $a): ?>
                <div class="deadline-item">
                  <div class="deadline-date-tile <?= $a['urgent'] ? 'urgent' : '' ?>">
                    <span class="deadline-tile-month"><?= htmlspecialchars($a['month'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="deadline-tile-day"><?= htmlspecialchars($a['day'], ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                  <div class="deadline-info">
                    <strong><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <small><?= htmlspecialchars($a['code'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($a['due_badge'], ENT_QUOTES, 'UTF-8') ?></small>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Submission Guidelines Card -->
          <div class="card" style="padding:20px;">
            <div class="card-header" style="padding:0 0 14px 0;margin-bottom:14px;">
              <h2 style="font-size:15px;display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Submission policy
              </h2>
            </div>
            <div class="guideline-list">
              <div class="guideline-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Submit laboratory source code directly to university git repository before the stated 11:59 PM deadline.</span>
              </div>
              <div class="guideline-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Include a comprehensive README report with test coverage and setup instructions for laboratory grading.</span>
              </div>
              <div class="guideline-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Submissions are audited against university academic integrity guidelines and peer plagiarism checks.</span>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
  </div>
</div>

<script>
(function() {
  'use strict';
  const STORAGE_KEY = 'studenthub-completed';

  function getCompleted() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch {
      return [];
    }
  }

  function setCompleted(list) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    } catch {}
  }

  const container = document.getElementById('assignmentContainer');
  const items = container ? Array.from(container.querySelectorAll('.assignment-item')) : [];
  const totalCount = items.length;

  function updateMetrics() {
    const completedList = getCompleted();
    let completedCount = 0;

    items.forEach(item => {
      const id = item.dataset.assignmentId;
      const chk = item.querySelector('.assignment-checkbox');
      const isDone = completedList.includes(id) || (!chk.dataset.userToggled && chk.checked);

      if (chk) chk.checked = isDone;
      item.dataset.status = isDone ? 'completed' : 'pending';

      const chip = item.querySelector('.assignment-due-chip');
      const chipText = item.querySelector('.chip-text');

      if (isDone) {
        completedCount++;
        item.classList.add('state-completed');
        item.classList.remove('state-urgent');
        if (chip) {
          chip.className = 'assignment-due-chip completed';
          chip.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span class="chip-text">Completed</span>';
        }
      } else {
        item.classList.remove('state-completed');
        if (item.dataset.search.includes('urgent') || item.dataset.assignmentId === 'a1' || item.dataset.assignmentId === 'a2') {
          item.classList.add('state-urgent');
          if (chip) {
            chip.className = 'assignment-due-chip urgent';
            chip.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg><span class="chip-text">Action required</span>';
          }
        }
      }
    });

    const pendingCount = totalCount - completedCount;
    const rate = Math.round((completedCount / totalCount) * 100);

    const pendingEl = document.getElementById('kpiPendingCount');
    if (pendingEl) pendingEl.textContent = pendingCount;

    const completedEl = document.getElementById('kpiCompletedCount');
    if (completedEl) completedEl.textContent = completedCount;

    const rateEl = document.getElementById('kpiCompletedRate');
    if (rateEl) rateEl.textContent = rate + '% completion rate';

    const countAllEl = document.getElementById('countAll');
    if (countAllEl) countAllEl.textContent = totalCount;

    const countPendingEl = document.getElementById('countPending');
    if (countPendingEl) countPendingEl.textContent = pendingCount;

    const countCompletedEl = document.getElementById('countCompleted');
    if (countCompletedEl) countCompletedEl.textContent = completedCount;

    const badgeTextEl = document.getElementById('pendingBadgeText');
    if (badgeTextEl) badgeTextEl.textContent = pendingCount + ' Pending deliverables';

    const progTextEl = document.getElementById('progressMetricText');
    if (progTextEl) progTextEl.textContent = completedCount + ' of ' + totalCount + ' deliverables completed (' + rate + '%)';

    const fillEl = document.getElementById('progressBarFill');
    if (fillEl) fillEl.style.width = rate + '%';
  }

  // Checkbox toggling
  document.addEventListener('change', function(e) {
    if (!e.target.matches('.assignment-checkbox')) return;
    const chk = e.target;
    chk.dataset.userToggled = 'true';
    const id = chk.dataset.assignmentChk;
    let completedList = getCompleted();

    if (chk.checked) {
      if (!completedList.includes(id)) completedList.push(id);
    } else {
      completedList = completedList.filter(x => x !== id);
    }
    setCompleted(completedList);
    updateMetrics();

    // Re-apply active filter
    const activeBtn = document.querySelector('.segmented-btn.active');
    if (activeBtn) applyFilter(activeBtn.dataset.filter);
  });

  // Filter tabs
  function applyFilter(filter) {
    const query = (document.getElementById('assignmentSearchInput')?.value || '').trim().toLowerCase();
    items.forEach(item => {
      const matchesFilter = filter === 'all' || item.dataset.status === filter;
      const matchesSearch = !query || item.dataset.search.includes(query);
      item.style.display = (matchesFilter && matchesSearch) ? 'grid' : 'none';
    });
  }

  document.querySelectorAll('.segmented-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.segmented-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      applyFilter(btn.dataset.filter);
    });
  });

  // Search input
  const searchInput = document.getElementById('assignmentSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const activeBtn = document.querySelector('.segmented-btn.active');
      applyFilter(activeBtn ? activeBtn.dataset.filter : 'all');
    });
  }

  // Initialize from storage
  const initialSaved = getCompleted();
  if (initialSaved.length === 0) {
    // initialize defaults: a4 and a5 were completed
    setCompleted(['a4', 'a5']);
  }
  updateMetrics();
})();
</script>
