<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'My courses';
$pageCrumb = 'Courses';
$activeRoute = 'courses';
$portalType = 'student';

// CHARUSAT CSPIT Department of CSE - B.Tech (CSE) Semester 3 (Odd Term) Enrolled Courses
$courses = [
    'CSUC201' => [
        'name' => 'Fundamentals of Data Structure and Algorithm',
        'short_name' => 'FDSA',
        'code' => 'CSUC201',
        'credits' => 4,
        'credit_scheme' => '3+1 (T+P)',
        'faculty' => 'Dr. Amit Thakkar (ART, HOD)',
        'faculty_team' => 'Dr. Amit Thakkar (ART), Prof. Avani Khokhariya (AVK), Prof. Gayatree Parbat (GP), Prof. Vidisha Pradhan (VMP)',
        'email' => 'amitthakkar.cse@charusat.ac.in',
        'attendance' => 92,
        'room' => 'Room 506 / Labs 632, 634, 638',
        'schedule' => 'Mon 09:10 AM, Tue 10:10 AM, Wed 03:20 PM, Fri 10:10 AM',
        'description' => 'Comprehensive study of linear and non-linear abstract data structures, dynamic memory management, linked lists, stacks, queues, trees (Binary Trees, BST, AVL), heap structures, graph representations, searching, sorting complexity, and greedy/dynamic problem solving.',
        'syllabus' => [
            'Unit 1: Introduction to Data Structures, Complexity Analysis (Big O, Omega, Theta)',
            'Unit 2: Linear Data Structures: Arrays, Stacks, Queues (Circular & Priority Queues), Linked Lists',
            'Unit 3: Non-Linear Structures: Binary Trees, Binary Search Trees, AVL Tree Rotations, Heaps',
            'Unit 4: Graph Algorithms: BFS, DFS, Dijkstra Shortest Path, Prim & Kruskal MST',
            'Unit 5: Searching & Sorting Techniques: QuickSort, MergeSort, RadixSort, Hash Tables'
        ]
    ],
    'ITUE204' => [
        'name' => 'Web Development Frameworks',
        'short_name' => 'WDF',
        'code' => 'ITUE204',
        'credits' => 5,
        'credit_scheme' => '3+2 (T+P)',
        'faculty' => 'Prof. Srushti Gajjar (SRG)',
        'faculty_team' => 'Prof. Srushti Gajjar (SRG), Prof. Vaibhavi Patel (VYP), Prof. Dhara Solanki (DSS), Prof. Brinda Patel (BPP), Prof. Akshita Kadam (ADK)',
        'email' => 'srushtigajjar.it@charusat.ac.in',
        'attendance' => 89,
        'room' => 'Room 506 / Labs 631, 632, 633',
        'schedule' => 'Tue 03:20 PM, Wed 02:20 PM, Thu 09:10 AM',
        'description' => 'End-to-end full-stack web engineering with industry-grade frameworks, responsive layouts, asynchronous state management, component architecture, client-side routing, REST APIs, middleware, authentication, session security, and database integration.',
        'syllabus' => [
            'Unit 1: Modern Web Foundations: Semantic HTML5, CSS Grid/Flexbox, ES6+ JavaScript & TypeScript',
            'Unit 2: Frontend Framework Architecture: Component Lifecycle, Props, Reactive State, Hooks',
            'Unit 3: Client-side Routing, Global State Containers, and Asynchronous Data Fetching',
            'Unit 4: Server-side Architecture, RESTful API Design, Middleware, JWT Authentication',
            'Unit 5: Full-Stack Integration, Relational/NoSQL Database Connectivity, and Cloud Deployment'
        ]
    ],
    'CEUE203' => [
        'name' => 'Object Oriented Programming',
        'short_name' => 'OOP',
        'code' => 'CEUE203',
        'credits' => 3,
        'credit_scheme' => '2+1 (T+P)',
        'faculty' => 'Prof. Siddharth Shah (SSS)',
        'faculty_team' => 'Prof. Siddharth Shah (SSS), Prof. Akshita Kadam (ADK)',
        'email' => 'siddharthshah.ce@charusat.ac.in',
        'attendance' => 94,
        'room' => 'Room 506 / Labs 631, 632, 638',
        'schedule' => 'Wed 10:10 AM, Fri 03:20 PM',
        'description' => 'Object-oriented programming paradigm, class structures, data encapsulation, access specifiers, constructor/destructor chains, operator overloading, inheritance hierarchies, dynamic polymorphism, virtual tables, exception handling, and generic template programming.',
        'syllabus' => [
            'Unit 1: Principles of Object-Oriented Methodology, Classes, Encapsulation, Scope Resolution',
            'Unit 2: Constructors, Destructors, Copy Semantics, and Operator Overloading',
            'Unit 3: Single, Multiple, Hierarchical, & Hybrid Inheritance, Virtual Base Classes',
            'Unit 4: Polymorphism, Virtual Functions, Pure Virtual Functions, and Abstract Classes',
            'Unit 5: Templates, Exception Handling, File Streams, and Standard Template Library (STL)'
        ]
    ],
    'ITUC201' => [
        'name' => 'Fundamentals of Computer Networks',
        'short_name' => 'FCN',
        'code' => 'ITUC201',
        'credits' => 5,
        'credit_scheme' => '4+1 (T+P)',
        'faculty' => 'Prof. Harshul Yagnik (HYY)',
        'faculty_team' => 'Prof. Harshul Yagnik (HYY), Prof. Brinda Patel (BPP)',
        'email' => 'harshulyagnik.it@charusat.ac.in',
        'attendance' => 88,
        'room' => 'Room 506 / Labs 631, 633, 638',
        'schedule' => 'Mon 10:10 AM, Tue 02:20 PM, Thu 10:10 AM, Fri 02:20 PM',
        'description' => 'Layered network architectures, OSI and TCP/IP protocol suites, physical layer signaling, data link flow/error control, HDLC, medium access sublayers (CSMA/CD), IP subnetting, routing protocols (RIP, OSPF, BGP), TCP connection lifecycle, UDP, DNS, and HTTP.',
        'syllabus' => [
            'Unit 1: Introduction to Data Communication, Network Topologies, Layered Reference Models',
            'Unit 2: Data Link Layer: Framing, Error Detection (CRC), Flow Control (Sliding Window)',
            'Unit 3: Medium Access Control: Ethernet, IEEE 802.11 Wireless, Switching & Bridging',
            'Unit 4: Network Layer: IPv4/IPv6 Addressing, Subnetting, CIDR, Distance Vector & Link State Routing',
            'Unit 5: Transport & Application Layers: TCP 3-Way Handshake, Flow/Congestion Control, DNS, HTTP'
        ]
    ],
    'MSUD203' => [
        'name' => 'Discrete Mathematics',
        'short_name' => 'MATHS',
        'code' => 'MSUD203',
        'credits' => 3,
        'credit_scheme' => '3+0 (T+P)',
        'faculty' => 'Prof. Pragnesh Makwana (PM)',
        'faculty_team' => 'Prof. Pragnesh Makwana (PM)',
        'email' => 'pragneshmakwana.maths@charusat.ac.in',
        'attendance' => 95,
        'room' => 'Room 506',
        'schedule' => 'Tue 09:10 AM, Wed 09:10 AM, Fri 09:10 AM',
        'description' => 'Discrete mathematical foundations for computing: propositional and predicate calculus, rules of inference, proof mechanisms (contraposition, contradiction, mathematical induction), set theory, relations, partially ordered sets, lattices, recurrence relations, and graph invariants.',
        'syllabus' => [
            'Unit 1: Mathematical Logic: Propositional Equivalences, Predicates, Quantifiers, Inference Rules',
            'Unit 2: Set Theory, Functions, Sequences, Sums, and Pigeonhole Principle',
            'Unit 3: Relations: Equivalence Relations, Partial Orderings, Hasse Diagrams, Lattices',
            'Unit 4: Combinatorics & Recurrence Relations: Linear Homogeneous & Non-homogeneous Recurrence',
            'Unit 5: Graph Theory Fundamentals: Isomorphism, Euler & Hamiltonian Graphs, Trees'
        ]
    ],
    'HSUV201' => [
        'name' => 'Creativity, Problem Solving and Innovation',
        'short_name' => 'HS',
        'code' => 'HSUV201',
        'credits' => 2,
        'credit_scheme' => '0+2 (T+P)',
        'faculty' => 'Prof. Akshita Kadam (ADK)',
        'faculty_team' => 'Prof. Akshita Kadam (ADK)',
        'email' => 'akshitakadam.hs@charusat.ac.in',
        'attendance' => 100,
        'room' => 'Room 506',
        'schedule' => 'Mon 12:10 PM - 02:10 PM',
        'description' => 'Interactive development of creative thinking, structured problem framing, human-centered design thinking, mind-mapping, lateral thinking exercises, prototyping, business model canvas formulation, and innovation pitch generation.',
        'syllabus' => [
            'Unit 1: Creative Thinking: Barriers to Creativity, Divergent vs Convergent Thinking Models',
            'Unit 2: Human-Centered Problem Solving: Empathy Mapping, User Persona Identification, Need Finding',
            'Unit 3: Ideation Techniques: Brainstorming, SCAMPER Framework, Six Thinking Hats',
            'Unit 4: Rapid Prototyping, Usability Testing, and Iterative Feedback Loops',
            'Unit 5: Intellectual Property Basics, Innovation Pitching, and Collaborative Case Studies'
        ]
    ]
];

$selectedCourseCode = $_GET['course'] ?? null;
$selectedCourse = ($selectedCourseCode && isset($courses[$selectedCourseCode])) ? $courses[$selectedCourseCode] : null;

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <?php if ($selectedCourse): ?>
        <!-- Single Course Detail View -->
        <div class="page-header">
          <div class="page-title-wrap">
            <a href="student_courses.php" class="btn btn-secondary btn-sm" style="margin-bottom:12px;display:inline-flex;">← Back to course list</a>
            <h1><?= htmlspecialchars($selectedCourse['name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($selectedCourse['code'], ENT_QUOTES, 'UTF-8') ?>)</h1>
            <p>CHARUSAT CSPIT · B.Tech (CSE) Semester 3 · Course syllabus, faculty team, and attendance standing.</p>
          </div>
          <div class="header-actions">
            <button type="button" class="btn btn-secondary btn-sm" data-action="print">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
              Print syllabus
            </button>
          </div>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <span class="stat-label">Credits</span>
            <strong class="stat-value tabular-nums"><?= $selectedCourse['credits'] ?></strong>
            <span class="stat-helper"><?= htmlspecialchars($selectedCourse['credit_scheme'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Attendance standing</span>
            <strong class="stat-value tabular-nums" style="<?= $selectedCourse['attendance'] < 80 ? 'color:var(--warning);' : '' ?>"><?= $selectedCourse['attendance'] ?>%</strong>
            <span class="stat-helper <?= $selectedCourse['attendance'] < 80 ? 'warning' : 'positive' ?>">
              <?= $selectedCourse['attendance'] < 80 ? 'Below 75% threshold warning' : 'Satisfactory attendance' ?>
            </span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Class location</span>
            <strong class="stat-value" style="font-size:16px;"><?= htmlspecialchars($selectedCourse['room'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="stat-helper"><?= htmlspecialchars($selectedCourse['schedule'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Faculty in-charge</span>
            <strong class="stat-value" style="font-size:16px;"><?= htmlspecialchars($selectedCourse['faculty'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="stat-helper"><?= htmlspecialchars($selectedCourse['email'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>

        <div class="two-column-layout">
          <section class="card">
            <div class="card-header">
              <h2>Course overview & description</h2>
              <span class="badge badge-info">Semester 3 Core</span>
            </div>
            <div class="card-body">
              <p style="margin:0 0 18px;color:var(--text);font-size:14px;line-height:1.7;">
                <?= htmlspecialchars($selectedCourse['description'], ENT_QUOTES, 'UTF-8') ?>
              </p>

              <h3 style="font-size:15px;margin:20px 0 12px;">Syllabus Modules</h3>
              <div style="display:flex;flex-direction:column;gap:10px;">
                <?php foreach ($selectedCourse['syllabus'] as $module): ?>
                  <div style="padding:12px 14px;border:1px solid var(--border-subtle);border-radius:var(--radius-control);background:var(--background);font-size:13px;display:flex;align-items:center;gap:10px;">
                    <span style="color:var(--primary);font-weight:700;">•</span>
                    <span><?= htmlspecialchars($module, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </section>

          <div class="card-stack">
            <section class="card">
              <div class="card-header">
                <h2>Faculty instruction team</h2>
              </div>
              <div class="card-body">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                  <div class="avatar" style="width:48px;height:48px;font-size:16px;">
                    <?= htmlspecialchars(app_get_initials($selectedCourse['faculty']), ENT_QUOTES, 'UTF-8') ?>
                  </div>
                  <div>
                    <strong style="display:block;font-size:15px;"><?= htmlspecialchars($selectedCourse['faculty'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <small style="color:var(--text-muted);">Lead Instructor · Department of CSE</small>
                  </div>
                </div>
                <div style="font-size:13px;color:var(--text-muted);display:flex;flex-direction:column;gap:8px;">
                  <div><strong>All Assigned Faculty:</strong> <?= htmlspecialchars($selectedCourse['faculty_team'], ENT_QUOTES, 'UTF-8') ?></div>
                  <div><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($selectedCourse['email'], ENT_QUOTES, 'UTF-8') ?>" style="color:var(--primary);"><?= htmlspecialchars($selectedCourse['email'], ENT_QUOTES, 'UTF-8') ?></a></div>
                  <div><strong>Department:</strong> Chandubhai S. Patel Institute of Technology (CSPIT)</div>
                </div>
              </div>
            </section>

            <section class="card">
              <div class="card-header">
                <h2>Academic links</h2>
              </div>
              <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                <a href="student_assignments.php" class="btn btn-secondary btn-sm" style="justify-content:flex-start;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                  View related deliverables
                </a>
                <a href="student_schedule.php" class="btn btn-secondary btn-sm" style="justify-content:flex-start;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                  View course timetable
                </a>
              </div>
            </section>
          </div>
        </div>

      <?php else: ?>
        <!-- Courses Grid Overview -->
        <div class="page-header">
          <div class="page-title-wrap">
            <h1>CHARUSAT Enrolled Courses</h1>
            <p>CHARUSAT · FTE · CSPIT · Department of CSE · Semester 3 (Odd Term) · 6 Enrolled Courses</p>
          </div>
          <div class="header-actions">
            <button type="button" class="btn btn-secondary btn-sm" data-action="print">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
              Print course directory
            </button>
          </div>
        </div>

        <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(340px, 1fr));gap:20px;">
          <?php foreach ($courses as $c): ?>
            <article class="card" data-search="<?= htmlspecialchars($c['name'] . ' ' . $c['code'] . ' ' . $c['short_name'] . ' ' . $c['faculty_team'], ENT_QUOTES, 'UTF-8') ?>" style="padding:22px;display:flex;flex-direction:column;justify-content:space-between;">
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                  <span class="badge badge-info"><?= htmlspecialchars($c['code'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($c['short_name'], ENT_QUOTES, 'UTF-8') ?>)</span>
                  <span class="badge <?= $c['attendance'] < 80 ? 'badge-warning' : 'badge-success' ?>">
                    <?= $c['attendance'] < 80 ? 'Review attendance' : 'On track' ?>
                  </span>
                </div>
                <h2 style="margin:0 0 6px;font-size:17px;line-height:1.4;">
                  <a href="student_courses.php?course=<?= urlencode($c['code']) ?>" style="color:var(--text);text-decoration:none;">
                    <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </h2>
                <p style="margin:0 0 14px;color:var(--text-muted);font-size:13px;">Faculty: <strong><?= htmlspecialchars($c['faculty'], ENT_QUOTES, 'UTF-8') ?></strong></p>

                <!-- Attendance Bar -->
                <div class="progress-wrap" style="margin-bottom:16px;">
                  <div class="progress-info">
                    <span>Course attendance</span>
                    <strong class="tabular-nums"><?= $c['attendance'] ?>%</strong>
                  </div>
                  <div class="progress-bar-track">
                    <div class="progress-bar-fill <?= $c['attendance'] < 80 ? 'warn' : '' ?>" style="width:<?= $c['attendance'] ?>%;"></div>
                  </div>
                  <?php if ($c['attendance'] < 80): ?>
                    <span class="attendance-warning-label">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                      Below 75% requirement
                    </span>
                  <?php endif; ?>
                </div>
              </div>

              <div style="padding-top:14px;border-top:1px solid var(--border-subtle);display:flex;justify-content:space-between;align-items:center;">
                <span class="tabular-nums" style="color:var(--text-muted);font-size:12px;"><?= $c['credit_scheme'] ?> · <?= htmlspecialchars($c['room'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="student_courses.php?course=<?= urlencode($c['code']) ?>" class="btn btn-secondary btn-sm">Course details →</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
  </div>
</div>
