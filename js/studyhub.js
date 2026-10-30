(() => {
  'use strict';

  function getAppPath(path) {
    const base = window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.includes('/student/') ? '../' : '');
    return base + path;
  }

  const content = document.getElementById('pageContent');
  const titleNode = document.querySelector('.crumb-current');
  const search = document.getElementById('pageSearch');
  
  const student = window.STUDENT_PROFILE || {};
  const studentName = student.name || student.full_name || 'Student';

  const titles = {
    overview: 'Overview',
    courses: 'My courses',
    schedule: 'Schedule',
    assignments: 'Assignments',
    attendance: 'Attendance',
    results: 'Results',
    events: 'Events',
    notices: 'Notices',
    profile: 'My profile',
    help: 'Help & Support'
  };

  const courses = [
    { name: 'Fundamentals of Data Structure and Algorithm', code: 'CSUC201', faculty: 'Dr. Amit Thakkar / Prof. Avani Khokhariya', attendance: 94, work: '3 of 4 assignments submitted', color: 'primary' },
    { name: 'Web Development Frameworks', code: 'ITUE204', faculty: 'Prof. Srushti Gajjar / Prof. Vaibhavi Patel', attendance: 95, work: '4 of 4 assignments submitted', color: 'info' },
    { name: 'Object Oriented Programming', code: 'CEUE203', faculty: 'Prof. Siddharth Shah / Prof. Akshita Kadam', attendance: 91, work: '2 of 3 assignments submitted', color: 'accent' },
    { name: 'Fundamentals of Computer Networks', code: 'ITUC201', faculty: 'Prof. Harshul Yagnik / Prof. Brinda Patel', attendance: 92, work: '3 of 3 assignments submitted', color: 'warning' },
    { name: 'Discrete Mathematics', code: 'MSUD203', faculty: 'Prof. Pragnesh Makwana', attendance: 93, work: 'All assignments submitted', color: 'primary' },
    { name: 'Creativity, Problem Solving and Innovation', code: 'HSUV201', faculty: 'Prof. Akshita Kadam', attendance: 100, work: 'All assignments submitted', color: 'success' }
  ];

  const assignments = [
    { 
      id: 'a1', 
      title: 'Binary Search Trees & AVL Balancing — Problem Set', 
      code: 'CSUC201', 
      course: 'Fundamentals of Data Structure and Algorithm',
      faculty: 'Dr. Amit Thakkar / Prof. Avani Khokhariya', 
      due: '02 Nov 2026', 
      dueTime: '11:59 PM',
      when: 'Due in 4 days', 
      state: 'Pending', 
      urgent: true,
      month: 'NOV',
      day: '02',
      submissionType: 'Code repository & PDF report',
      desc: 'Implement recursive insert, delete, and balancing rotations on an AVL tree. Include empirical runtime benchmarks against unbalanced BSTs.' 
    },
    { 
      id: 'a2', 
      title: 'Responsive Single-Page Application (SPA) Milestone', 
      code: 'ITUE204', 
      course: 'Web Development Frameworks',
      faculty: 'Prof. Srushti Gajjar / Prof. Vaibhavi Patel', 
      due: '04 Nov 2026', 
      dueTime: '11:59 PM',
      when: 'Due in 6 days', 
      state: 'Pending', 
      urgent: true,
      month: 'NOV',
      day: '04',
      submissionType: 'GitHub repo & component documentation',
      desc: 'Develop a modular web application with clean separation of concerns, modern tokenized CSS design system, and client-side reactive state.' 
    },
    { 
      id: 'a3', 
      title: 'Polymorphism & Virtual File Persistence Lab Worksheet', 
      code: 'CEUE203', 
      course: 'Object Oriented Programming',
      faculty: 'Prof. Siddharth Shah / Prof. Akshita Kadam', 
      due: '06 Nov 2026', 
      dueTime: '05:00 PM',
      when: 'Due in 8 days', 
      state: 'Pending', 
      urgent: false,
      month: 'NOV',
      day: '06',
      submissionType: 'C++ / Java source code & execution output',
      desc: 'Implement an academic entity inheritance model demonstrating dynamic binding, abstract base classes, and serialized file stream persistence.' 
    },
    { 
      id: 'a4', 
      title: 'Subnetting, CIDR & Wireshark Packet Analysis', 
      code: 'ITUC201', 
      course: 'Fundamentals of Computer Networks',
      faculty: 'Prof. Harshul Yagnik / Prof. Brinda Patel', 
      due: '28 Oct 2026', 
      dueTime: '11:59 PM',
      when: 'Submitted', 
      state: 'Submitted', 
      urgent: false,
      month: 'OCT',
      day: '28',
      submissionType: 'Submitted online',
      desc: 'Calculate variable length subnet masks (VLSM), route aggregation prefixes, and inspect captured TCP 3-way handshake packets.' 
    },
    { 
      id: 'a5', 
      title: 'Planar Graphs & Recurrence Relations Proofs', 
      code: 'MSUD203', 
      course: 'Discrete Mathematics',
      faculty: 'Prof. Pragnesh Makwana', 
      due: '24 Oct 2026', 
      dueTime: '11:59 PM',
      when: 'Graded · 94 / 100', 
      state: 'Completed', 
      urgent: false,
      month: 'OCT',
      day: '24',
      submissionType: 'Graded submission',
      desc: 'Formal mathematical induction proofs for graph coloring bounds, Euler characteristic formula, and generating function solutions.' 
    },
    { 
      id: 'a6', 
      title: 'Design Thinking & Innovation Sprint Canvas', 
      code: 'HSUV201', 
      course: 'Creativity, Problem Solving and Innovation',
      faculty: 'Prof. Akshita Kadam', 
      due: '20 Oct 2026', 
      dueTime: '11:59 PM',
      when: 'Graded · 96 / 100', 
      state: 'Completed', 
      urgent: false,
      month: 'OCT',
      day: '20',
      submissionType: 'Prototype deck & evaluation canvas',
      desc: 'Synthesize empathize-define-ideate stage insights into a viable campus resource-optimization concept.' 
    }
  ];

  const weekdaySchedules = {
    1: [
      ['09:10', 'CSUC201 · FDSA (Theory)', 'Room 506 · Prof. Vidisha Pradhan (VMP)', 'primary'],
      ['10:10', 'ITUC201 · FCN (Theory)', 'Room 506 · Prof. Harshul Yagnik (HYY)', 'info'],
      ['11:10', 'Lunch break', 'Campus Dining Hall · 1 Hour', 'neutral'],
      ['12:10', 'HSUV201 · HS (Theory/Practical)', 'Room 506 · Prof. Akshita Kadam (ADK)', 'primary'],
      ['14:20', 'Laboratory session', 'A1: CSUC201 (632-AVK) | B1: ITUC201 (638-BPP) | C1: ITUE204 (633-ADK)', 'warning']
    ],
    2: [
      ['09:10', 'MSUD203 · MATHS (Theory)', 'Room 506 · Prof. Pragnesh Makwana (PM)', 'primary'],
      ['10:10', 'CSUC201 · FDSA (Theory)', 'Room 506 · Dr. Amit Thakkar (ART)', 'primary'],
      ['11:10', 'Lunch break', 'Campus Dining Hall · 1 Hour', 'neutral'],
      ['12:10', 'Laboratory session', 'A1: ITUC201 (633-BPP) | B1: CEUE203 (632-ADK) | C1: ITUC201 (631-HYY)', 'warning'],
      ['14:20', 'ITUC201 · FCN (Theory)', 'Room 506 · Prof. Harshul Yagnik (HYY)', 'info'],
      ['15:20', 'ITUE204 · WDF (Theory)', 'Room 506 · Prof. Srushti Gajjar (SRG)', 'accent']
    ],
    3: [
      ['09:10', 'MSUD203 · MATHS (Theory)', 'Room 506 · Prof. Pragnesh Makwana (PM)', 'primary'],
      ['10:10', 'CEUE203 · OOP (Theory)', 'Room 506 · Prof. Siddharth Shah (SSS)', 'info'],
      ['11:10', 'Lunch break', 'Campus Dining Hall · 1 Hour', 'neutral'],
      ['12:10', 'Laboratory session', 'A1: ITUE204 (633-DSS) | B1: ITUE204 (632-BPP) | C1: CSUC201 (634-AVK)', 'warning'],
      ['14:20', 'ITUE204 · WDF (Theory)', 'Room 506 · Prof. Srushti Gajjar (SRG)', 'accent'],
      ['15:20', 'CSUC201 · FDSA (Theory)', 'Room 506 · Prof. Vidisha Pradhan (VMP)', 'primary']
    ],
    4: [
      ['09:10', 'ITUE204 · WDF (Theory)', 'Room 506 · Prof. Vaibhavi Patel (VYP)', 'accent'],
      ['10:10', 'ITUC201 · FCN (Theory)', 'Room 506 · Prof. Harshul Yagnik (HYY)', 'info'],
      ['11:10', 'Lunch break', 'Campus Dining Hall · 1 Hour', 'neutral'],
      ['12:10', 'Laboratory session 1', 'A1: ITUE204 (631-VYP) | B1: CSUC201 (638-AVK) | C1: ITUE204 (632-DSS)', 'warning'],
      ['14:20', 'Laboratory session 2', 'A1: CEUE203 (638-ADK) | B1: ITUE204 (632-BPP) | C1: CEUE203 (631-SSS)', 'warning']
    ],
    5: [
      ['09:10', 'MSUD203 · MATHS (Theory)', 'Room 506 · Prof. Pragnesh Makwana (PM)', 'primary'],
      ['10:10', 'CSUC201 · FDSA (Theory)', 'Room 506 · Dr. Amit Thakkar (ART)', 'primary'],
      ['11:10', 'Lunch break', 'Campus Dining Hall · 1 Hour', 'neutral'],
      ['12:10', 'Counselling session', 'A1: CS-631 (SRG) | B1: CS-632 (VYP) | C1: CS-633 (ADK)', 'info'],
      ['14:20', 'ITUC201 · FCN (Theory)', 'Room 506 · Prof. Harshul Yagnik (HYY)', 'info'],
      ['15:20', 'CEUE203 · OOP (Theory)', 'Room 506 · Prof. Siddharth Shah (SSS)', 'accent']
    ]
  };

  let scheduleDate = new Date();
  scheduleDate.setHours(12, 0, 0, 0);
  const dailySchedule = date => weekdaySchedules[date.getDay()] || [];
  const saved = () => { try { return JSON.parse(localStorage.getItem('studenthub-completed') || '[]'); } catch { return []; } };
  const esc = (value) => String(value).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  const getNoticeBadge = (cat) => {
    const c = String(cat || '').toLowerCase().trim();
    if (c === 'academic') return 'badge-warning';
    if (c === 'library') return 'badge-info';
    if (c === 'events') return 'badge-success';
    return 'badge-info';
  };

  async function syncCampusNotices() {
    try {
      const res = await fetch(getAppPath('api/notices_api.php'), { credentials: 'same-origin' });
      const data = await res.json();
      if (data && data.success && Array.isArray(data.notices)) {
        window.CAMPUS_NOTICES = data.notices;
      }
    } catch {}
  }
  syncCampusNotices();

  // Shared UI builders (Sentence Case, Tokens, Accessibility)
  const heading = (title, description, action = '') => `
    <div class="page-header">
      <div class="page-title-wrap">
        <h1>${esc(title)}</h1>
        <p>${esc(description)}</p>
      </div>
      ${action ? `<div class="header-actions">${action}</div>` : ''}
    </div>
  `;

  const card = (title, body, link = '') => `
    <section class="card">
      <div class="card-header">
        <h2>${esc(title)}</h2>
        ${link}
      </div>
      <div class="card-body">${body}</div>
    </section>
  `;

  // Fix Problem 13: Progress bar with text indicator when below threshold
  const progress = (label, value, warn = false) => `
    <div class="progress-wrap">
      <div class="progress-info">
        <strong>${esc(label)}</strong>
        <span class="tabular-nums">${value}%</span>
      </div>
      <div class="progress-bar-track">
        <div class="progress-bar-fill ${warn ? 'warn' : ''}" style="width:${value}%"></div>
      </div>
      ${warn ? '<span class="attendance-warning-label"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Below 75% requirement</span>' : ''}
    </div>
  `;

  const tasksHtml = (limit = 3) => assignments.filter(x => x.state === 'Pending').slice(0, limit).map(x => `
    <div class="task-row" data-search="${esc(x.title + ' ' + x.code)}">
      <span class="task-mark"></span>
      <span class="task-main">
        <strong>${esc(x.title)}</strong>
        <small>${esc(x.code)} · ${esc(x.when)}</small>
      </span>
      <span class="task-deadline tabular-nums">${esc(x.due)}</span>
    </div>
  `).join('');

  const scheduleHtml = (list = dailySchedule(new Date()).slice(0, 4)) => `
    <div class="schedule-list">
      ${list.length ? list.map((x, i) => `
        <div class="schedule-row" data-search="${esc(x.join(' '))}">
          <span class="schedule-time tabular-nums">${x[0]}</span>
          <span class="schedule-course">
            <strong>${esc(x[1])}</strong>
            <small>${esc(x[2])}</small>
          </span>
          <span class="schedule-room">${i === 0 ? '<span class="badge badge-success">Current</span>' : ''}</span>
        </div>
      `).join('') : '<p class="empty-state">No classes scheduled for today.</p>'}
    </div>
  `;

  const courseCards = () => `
    <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:20px;">
      ${courses.map(c => `
        <article class="card" data-search="${esc(c.name + ' ' + c.code + ' ' + c.faculty)}" style="padding:20px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <span class="badge badge-info">${c.code}</span>
            <span class="badge ${c.attendance < 80 ? 'badge-warning' : 'badge-success'}">${c.attendance < 80 ? 'Review attendance' : 'On track'}</span>
          </div>
          <h3 style="margin:0 0 6px;font-size:16px;">${esc(c.name)}</h3>
          <p style="margin:0 0 16px;color:var(--text-muted);font-size:13px;">${esc(c.faculty)}</p>
          ${progress('Course attendance', c.attendance, c.attendance < 80)}
          <div style="margin-top:16px;padding-top:12px;border-top:1px solid var(--border-subtle);display:flex;justify-content:space-between;align-items:center;">
            <small style="color:var(--text-muted);font-size:12px;">${esc(c.work)}</small>
            <a class="text-link" href="student_courses.php?course=${encodeURIComponent(c.code)}">Details →</a>
          </div>
        </article>
      `).join('')}
    </div>
  `;

  // Fix Problem 15: Hero card made actionable with real student name & summary
  const overview = () => `
    ${heading('Academic overview', 'Review upcoming deadlines, today’s classes, and course progress from one workspace.')}
    
    <section class="hero-card">
      <div class="hero-content">
        <img src="${getAppPath('images/charusat-logo.png')}" alt="CHARUSAT University Logo" style="height:38px;width:auto;object-fit:contain;margin-bottom:10px;display:block;">
        <h2>Hello, ${esc(studentName)}</h2>
        <p>You have 3 assignments due this week and your next class begins at 09:00 AM. Keep up the great pace!</p>
      </div>
      <div class="hero-actions">
        <a href="student_assignments.php" class="btn btn-primary btn-sm">Review assignments →</a>
        <a href="student_schedule.php" class="btn btn-secondary btn-sm">View timetable</a>
        <a href="student_help.php" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
          Help & FAQs
        </a>
      </div>
    </section>

    <!-- Fix Problems 2 & 4: Stat cards without numbers, compact, with icon, value, helper line -->
    <div class="stats-grid">
      <a href="student_attendance.php" class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Attendance</span>
          <div class="stat-icon-wrap success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
          </div>
        </div>
        <strong class="stat-value tabular-nums">93.6%</strong>
        <span class="stat-helper positive">Above 75% requirement</span>
      </a>

      <a href="student_results.php" class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Current GPA</span>
          <div class="stat-icon-wrap info">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
          </div>
        </div>
        <strong class="stat-value tabular-nums">8.95 <small>/ 10</small></strong>
        <span class="stat-helper">Semester 3 standing</span>
      </a>

      <a href="student_courses.php" class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Courses enrolled</span>
          <div class="stat-icon-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
          </div>
        </div>
        <strong class="stat-value tabular-nums">6</strong>
        <span class="stat-helper">Semester 3 (Odd Term)</span>
      </a>

      <a href="student_assignments.php" class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Due this week</span>
          <div class="stat-icon-wrap amber">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
          </div>
        </div>
        <strong class="stat-value tabular-nums" style="color:var(--warning);">3</strong>
        <span class="stat-helper warning">Next due in 4 days</span>
      </a>
    </div>

    <div class="content-grid">
      <div class="column-stack">
        ${card('Upcoming assignments', '<div class="task-list">' + tasksHtml(3) + '</div>', '<a class="text-link" href="student_assignments.php">View all →</a>')}
        ${card('Today’s schedule', scheduleHtml(), '<a class="text-link" href="student_schedule.php">Full schedule →</a>')}
        ${card('Quick navigation', `
          <div class="grid" style="grid-template-columns:repeat(3, 1fr);gap:12px;">
            <a class="btn btn-secondary" style="flex-direction:column;padding:16px 12px;gap:8px;" href="student_courses.php">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary)"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
              <span>My courses</span>
            </a>
            <a class="btn btn-secondary" style="flex-direction:column;padding:16px 12px;gap:8px;" href="student_attendance.php">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary)"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>Attendance</span>
            </a>
            <a class="btn btn-secondary" style="flex-direction:column;padding:16px 12px;gap:8px;" href="student_results.php">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary)"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
              <span>Results</span>
            </a>
          </div>
        `)}
      </div>

      <div class="column-stack">
        ${card('Course attendance', '<div class="progress-list">' + courses.slice(0, 4).map(c => progress(c.name + ' (' + c.code + ')', c.attendance, c.attendance < 80)).join('') + '</div>', '<a class="text-link" href="student_attendance.php">Details →</a>')}
        ${card('Latest notices', (() => {
          const list = Array.isArray(window.CAMPUS_NOTICES) ? window.CAMPUS_NOTICES : [];
          if (!list.length) return '<p style="color:var(--text-muted);font-size:13px;margin:0;">No new notices at this time.</p>';
          return '<div style="display:flex;flex-direction:column;gap:14px;">' + list.slice(0, 3).map((n, i) => `
            <div style="${i > 0 ? 'padding-top:12px;border-top:1px solid var(--border-subtle);' : ''}">
              <span class="badge ${getNoticeBadge(n.category)}" style="margin-bottom:6px;">${esc(n.category || 'General')}</span>
              <strong style="display:block;font-size:13px;color:var(--text);">${esc(n.title)}</strong>
              <small style="color:var(--text-muted);">${esc(n.author || 'Administration')} · ${esc(n.date)}</small>
            </div>
          `).join('') + '</div>';
        })(), '<a class="text-link" href="student_notices.php">All notices →</a>')}
      </div>
    </div>

    <div class="alert alert-info" style="margin-top:24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
      <div>This project preview displays academic demonstration data. Retained from Practicals 1 to 14.</div>
    </div>
  `;

  const coursesPage = () => `
    ${heading('My courses', 'Enrolled courses, faculty details, and syllabus progress.', '<button class="btn btn-secondary btn-sm" type="button" data-action="print">Print course list</button>')}
    ${courseCards()}
  `;

  const schedulePage = () => {
    const monday = new Date(scheduleDate);
    monday.setDate(monday.getDate() - ((monday.getDay() + 6) % 7));
    const days = Array.from({ length: 5 }, (_, i) => { const d = new Date(monday); d.setDate(monday.getDate() + i); return d; });
    const format = (date, options) => new Intl.DateTimeFormat('en', options).format(date);
    const actions = '<a class="btn btn-secondary btn-sm" href="student_schedule.php?view=matrix">Full official timetable ↗</a><button class="btn btn-secondary btn-sm" type="button" data-action="print">Print</button>';
    const dayButtons = days.map((day, idx) => `
      <button class="btn ${day.toDateString() === scheduleDate.toDateString() ? 'btn-primary' : 'btn-secondary'} btn-sm" type="button" data-schedule-day data-year="${day.getFullYear()}" data-month="${day.getMonth()}" data-day="${day.getDate()}">
        <span>${['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'][idx]}</span>
      </button>
    `).join(' ');
    const rows = dailySchedule(scheduleDate);
    const blocks = rows.length ? rows.map(x => `
      <div class="timeline-item" data-search="${esc(x.join(' '))}">
        <span class="timeline-time tabular-nums">${esc(x[0])}</span>
        <div class="timeline-details" style="flex:1;">
          <strong>${esc(x[1])}</strong>
          <small>📍 ${esc(x[2])}</small>
        </div>
        <span class="badge ${x[3] === 'warning' ? 'badge-warning' : (x[3] === 'info' ? 'badge-info' : 'badge-primary')}">
          ${x[3] === 'warning' ? 'Lab Practical' : (x[3] === 'neutral' ? 'Break' : 'Theory')}
        </span>
      </div>
    `).join('') : '<div class="empty-state">No classes are scheduled for this day.</div>';

    return `
      ${heading('Class schedule', 'Browse the weekly timetable and select any day.', actions)}

      <!-- Institutional Information Strip -->
      <div class="card" style="padding:14px 18px;margin-bottom:18px;background:var(--surface);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-left:4px solid var(--primary);">
        <div style="display:flex;align-items:center;gap:12px;">
          <img src="images/charusat-logo.png" alt="CHARUSAT Logo" style="height:28px;width:auto;">
          <div>
            <strong style="font-size:13px;color:var(--text);display:block;">CSPIT Department of Computer Science & Engineering</strong>
            <small style="color:var(--text-muted);font-size:12px;">Academic Year 2026–27 · Semester 3 (Odd Term) · Division - I · Room 506</small>
          </div>
        </div>
        <div style="display:flex;gap:8px;">
          <a href="student_schedule.php?view=matrix" class="badge badge-info" style="text-decoration:none;">View master grid →</a>
        </div>
      </div>

      <section class="card" style="padding:22px;">
        <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">${dayButtons}</div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h2 style="font-size:16px;margin:0;">${format(scheduleDate, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</h2>
          <span class="badge badge-neutral">Room 506 & Specialized Labs</span>
        </div>
        <div class="timeline-list">${blocks}</div>
      </section>
    `;
  };

  const assignmentPage = () => {
    const done = saved();
    const completedCount = assignments.filter(a => a.state !== 'Pending' || done.includes(a.id)).length;
    const totalCount = assignments.length;
    const pendingCount = totalCount - completedCount;
    const completionRate = Math.round((completedCount / totalCount) * 100);

    const items = assignments.map(a => {
      const isDone = a.state !== 'Pending' || done.includes(a.id);
      const stateClass = isDone ? 'state-completed' : (a.urgent ? 'state-urgent' : '');
      const chipClass = isDone ? 'completed' : (a.urgent ? 'urgent' : (a.state === 'Submitted' ? 'submitted' : 'normal'));
      const chipText = isDone ? 'Completed' : (a.urgent ? 'Action required' : (a.state === 'Submitted' ? 'Submitted' : esc(a.when)));

      return `
        <article class="assignment-item ${stateClass}" data-assignment-id="${a.id}" data-state="${isDone ? 'completed' : 'pending'}" data-search="${esc((a.title + ' ' + a.code + ' ' + a.course + ' ' + a.faculty).toLowerCase())}">
          <div class="assignment-check-wrapper">
            <input type="checkbox" class="assignment-checkbox" data-assignment="${a.id}" ${isDone ? 'checked' : ''} aria-label="Mark ${esc(a.title)} complete">
          </div>

          <div class="assignment-content">
            <div class="assignment-header-row">
              <span class="badge badge-info">${esc(a.code)}</span>
              <span class="assignment-course-name">${esc(a.course)}</span>
            </div>

            <h2 class="assignment-title" style="margin:0 0 6px;font-size:15px;font-weight:600;color:var(--text);${isDone ? 'text-decoration:line-through;color:var(--text-muted);' : ''}">
              ${esc(a.title)}
            </h2>

            <p class="assignment-desc">
              ${esc(a.desc)}
            </p>

            <div class="assignment-meta-row">
              <span class="assignment-meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Due ${esc(a.due)}, ${esc(a.dueTime)}
              </span>
              <span class="assignment-meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                ${esc(a.submissionType)}
              </span>
              <span class="assignment-meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                ${esc(a.faculty)}
              </span>
            </div>
          </div>

          <div class="assignment-status-col">
            <span class="assignment-due-chip ${chipClass}">
              ${isDone ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>' : (a.urgent ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>' : '')}
              <span class="chip-text">${chipText}</span>
            </span>

            <span class="assignment-deadline-text">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              ${esc(a.due)}
            </span>
          </div>
        </article>
      `;
    }).join('');

    const timelineItems = assignments.map(a => `
      <div class="deadline-item">
        <div class="deadline-date-tile ${a.urgent ? 'urgent' : ''}">
          <span class="deadline-tile-month">${esc(a.month)}</span>
          <span class="deadline-tile-day">${esc(a.day)}</span>
        </div>
        <div class="deadline-info">
          <strong>${esc(a.title)}</strong>
          <small>${esc(a.code)} · ${esc(a.when)}</small>
        </div>
      </div>
    `).join('');

    return `
      ${heading(
        'Assignments & deliverables',
        'Track your homework, lab worksheets, and project milestones across all enrolled courses.',
        '<span class="badge badge-amber" style="padding:6px 14px;font-size:13px;font-weight:700;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg><span id="spaPendingBadgeText">' + pendingCount + ' Pending deliverables</span></span>'
      )}

      <!-- KPI Summary Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total assigned</span>
            <div class="stat-icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
          </div>
          <strong class="stat-value tabular-nums">${totalCount}</strong>
          <span class="stat-helper">Fall semester 2026–27</span>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">To do / Pending</span>
            <div class="stat-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
          </div>
          <strong class="stat-value tabular-nums" id="spaKpiPending" style="color:var(--warning);">${pendingCount}</strong>
          <span class="stat-helper warning">Action required</span>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Completed & graded</span>
            <div class="stat-icon-wrap success">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
          </div>
          <strong class="stat-value tabular-nums" id="spaKpiCompleted" style="color:var(--success);">${completedCount}</strong>
          <span class="stat-helper positive" id="spaKpiRate">${completionRate}% completion rate</span>
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
          <span class="coursework-progress-metric" id="spaProgressMetricText">${completedCount} of ${totalCount} deliverables completed (${completionRate}%)</span>
        </div>
        <div class="progress-bar-track" style="height:8px;border-radius:var(--radius-pill);background:var(--border-subtle);overflow:hidden;">
          <div class="progress-bar-fill" id="spaProgressBarFill" style="width:${completionRate}%;height:100%;background:var(--primary);border-radius:var(--radius-pill);transition:width 0.3s ease;"></div>
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
                <span class="segmented-count" id="spaCountAll">${totalCount}</span>
              </button>
              <button type="button" class="segmented-btn" data-filter="pending">
                To do
                <span class="segmented-count" id="spaCountPending">${pendingCount}</span>
              </button>
              <button type="button" class="segmented-btn" data-filter="completed">
                Completed
                <span class="segmented-count" id="spaCountCompleted">${completedCount}</span>
              </button>
            </div>

            <div style="position:relative;flex:1;max-width:280px;min-width:200px;">
              <input type="text" id="spaAssignmentSearch" placeholder="Filter by title, code..." class="form-control" style="padding-left:32px;font-size:13px;height:36px;border-radius:var(--radius-control);">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:10px;top:10px;color:var(--text-muted);pointer-events:none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
          </div>

          <!-- Assignment Cards List -->
          <div class="assignment-list" id="assignmentList">${items}</div>

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

            <div class="deadline-timeline">${timelineItems}</div>
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
    `;
  };

  const attendancePage = () => `
    ${heading('Attendance breakdown', 'Review per-course attendance and monitor courses requiring attention.')}
    <div class="stats-grid">
      <div class="stat-card">
        <span class="stat-label">Overall attendance</span>
        <strong class="stat-value tabular-nums">93.6%</strong>
        <span class="stat-helper positive">Above university requirement</span>
      </div>
      <div class="stat-card">
        <span class="stat-label">Classes attended</span>
        <strong class="stat-value tabular-nums">184 / 196</strong>
        <span class="stat-helper">Total contact hours</span>
      </div>
      <div class="stat-card">
        <span class="stat-label">Below 75% threshold</span>
        <strong class="stat-value tabular-nums" style="color:var(--success);">0 courses</strong>
        <span class="stat-helper positive">Good standing</span>
      </div>
      <div class="stat-card">
        <span class="stat-label">Course compliance</span>
        <strong class="stat-value tabular-nums" style="color:var(--success);">6 of 6</strong>
        <span class="stat-helper positive">100% eligibility confirmed</span>
      </div>
    </div>

    <section class="card">
      <div class="card-header">
        <h2>Course attendance register</h2>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">Course</th>
              <th scope="col" class="col-numeric">Classes held</th>
              <th scope="col" class="col-numeric">Attended</th>
              <th scope="col" class="col-numeric">Percentage</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            ${courses.map(c => {
              const heldMap = { CSUC201: 36, ITUE204: 40, CEUE203: 32, ITUC201: 38, MSUD203: 30, HSUV201: 20 };
              const held = heldMap[c.code] || 36;
              const attended = Math.round((held * c.attendance) / 100);
              return `
              <tr data-search="${esc(c.name + ' ' + c.code)}">
                <td>
                  <strong>${esc(c.name)}</strong>
                  <span style="display:block;font-size:11px;color:var(--text-muted);">${c.code} · ${esc(c.faculty)}</span>
                </td>
                <td class="col-numeric">${held}</td>
                <td class="col-numeric">${attended}</td>
                <td class="col-numeric"><strong>${c.attendance}%</strong></td>
                <td>
                  <span class="badge ${c.attendance < 80 ? 'badge-warning' : 'badge-success'}">
                    ${c.attendance < 80 ? 'Review required' : 'Satisfactory'}
                  </span>
                </td>
              </tr>
            `;}).join('')}
          </tbody>
        </table>
      </div>
    </section>
  `;

  const resultsPage = () => `
    ${heading('Semester results', 'Detailed breakdown of course credits, internal assessments, and GPA.', '<button class="btn btn-secondary btn-sm" type="button" data-action="print">Print transcript</button>')}
    <div class="hero-card">
      <div class="hero-content">
        <h2>Cumulative GPA: 8.95 / 10.00</h2>
        <p>Current academic status: First Class with Distinction · 63 of 130 Total Credits Completed (Semester 3 Current)</p>
      </div>
    </div>
    <section class="card" style="margin-top:20px;">
      <div class="card-header">
        <div>
          <h2>Semester 3 (Odd Term 2026–27) course grades</h2>
          <p>CSPIT Computer Science & Engineering · Division I Room 506</p>
        </div>
        <span class="badge badge-info">Term 3 SGPA: 9.48</span>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">Course</th>
              <th scope="col" class="col-numeric">Credits</th>
              <th scope="col" class="col-numeric">Coursework</th>
              <th scope="col" class="col-numeric">End-sem</th>
              <th scope="col">Grade</th>
              <th scope="col" class="col-numeric">Grade point</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Fundamentals of Data Structure and Algorithm</strong> (CSUC201)</td>
              <td class="col-numeric">4</td>
              <td class="col-numeric">94%</td>
              <td class="col-numeric">92%</td>
              <td><span class="badge badge-success">A+</span></td>
              <td class="col-numeric">9.5</td>
            </tr>
            <tr>
              <td><strong>Web Development Frameworks</strong> (ITUE204)</td>
              <td class="col-numeric">5</td>
              <td class="col-numeric">96%</td>
              <td class="col-numeric">98%</td>
              <td><span class="badge badge-success">A+</span></td>
              <td class="col-numeric">10.0</td>
            </tr>
            <tr>
              <td><strong>Object Oriented Programming</strong> (CEUE203)</td>
              <td class="col-numeric">3</td>
              <td class="col-numeric">88%</td>
              <td class="col-numeric">90%</td>
              <td><span class="badge badge-success">A</span></td>
              <td class="col-numeric">9.0</td>
            </tr>
            <tr>
              <td><strong>Fundamentals of Computer Networks</strong> (ITUC201)</td>
              <td class="col-numeric">5</td>
              <td class="col-numeric">90%</td>
              <td class="col-numeric">88%</td>
              <td><span class="badge badge-success">A</span></td>
              <td class="col-numeric">9.0</td>
            </tr>
            <tr>
              <td><strong>Discrete Mathematics</strong> (MSUD203)</td>
              <td class="col-numeric">3</td>
              <td class="col-numeric">92%</td>
              <td class="col-numeric">95%</td>
              <td><span class="badge badge-success">A+</span></td>
              <td class="col-numeric">9.5</td>
            </tr>
            <tr>
              <td><strong>Creativity, Problem Solving and Innovation</strong> (HSUV201)</td>
              <td class="col-numeric">2</td>
              <td class="col-numeric">98%</td>
              <td class="col-numeric">96%</td>
              <td><span class="badge badge-success">A+</span></td>
              <td class="col-numeric">10.0</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  `;

  const noticesPage = () => {
    const list = Array.isArray(window.CAMPUS_NOTICES) ? window.CAMPUS_NOTICES : [];
    const countBadge = `<span class="badge badge-info">${list.length} Published ${list.length === 1 ? 'notice' : 'notices'}</span>`;

    if (!list.length) {
      return `
        ${heading('Campus notices', 'Official communications, exam updates, and university circulars.', countBadge)}
        <div class="card" style="padding:48px 24px;text-align:center;">
          <h3 style="margin:0 0 6px;font-size:16px;">No campus notices at this time</h3>
          <p style="color:var(--text-muted);font-size:13px;">New announcements published by the administration will appear here.</p>
        </div>
      `;
    }

    const items = list.map(n => `
      <article class="card" style="padding:22px;" data-search="${esc((n.title + ' ' + (n.category || '') + ' ' + (n.author || '')).toLowerCase())}">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
          <span class="badge ${getNoticeBadge(n.category)}">${esc(n.category || 'General')}</span>
          <time class="tabular-nums" style="color:var(--text-muted);font-size:12px;">${esc(n.date)}</time>
        </div>
        <h3 style="margin:0 0 8px;font-size:16px;line-height:1.4;">${esc(n.title)}</h3>
        <p style="margin:0 0 14px;color:var(--text);font-size:14px;line-height:1.6;">${esc(n.content)}</p>
        <div style="padding-top:10px;border-top:1px solid var(--border-subtle);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
          <small style="color:var(--text-muted);">Issued by: <strong>${esc(n.author || 'Administration')}</strong></small>
          <span style="font-size:11px;color:var(--text-light);">Ref: SH-NOT-${String(n.id || 1).slice(-4)}</span>
        </div>
      </article>
    `).join('');

    return `
      ${heading('Campus notices', 'Official communications, exam updates, and university circulars.', countBadge)}
      <div style="display:flex;flex-direction:column;gap:16px;">${items}</div>
    `;
  };

  let eventRecords = [];
  let eventDatabaseReady = false;
  let eventFeedbackText = '';

  const eventsPage = () => `
    ${heading('Events & opportunities', 'Explore campus workshops, technical hackathons, guest lectures, and student meetups.')}
    <div class="card-header" style="margin-bottom:16px;background:var(--surface);border-radius:var(--radius-card);border:1px solid var(--border);">
      <div>
        <strong id="eventsTotal" class="tabular-nums" style="font-size:18px;color:var(--primary);">${eventRecords.length}</strong>
        <span style="color:var(--text-muted);font-size:13px;"> upcoming campus programs</span>
      </div>
      <span class="badge badge-info" id="eventsSource">${eventDatabaseReady ? 'Live database feed' : 'Local event preview'}</span>
    </div>

    <div id="eventFeedback" class="alert alert-info" style="display:${eventFeedbackText ? 'flex' : 'none'};">
      ${esc(eventFeedbackText)}
    </div>

    <div id="event-list" class="grid" style="grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;">
      <div style="padding:32px;text-align:center;grid-column:1/-1;">Loading campus event calendar...</div>
    </div>
  `;

  function renderEventsList() {
    const grid = document.getElementById('event-list');
    if (!grid) return;

    if (!eventRecords.length) {
      grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;padding:48px;text-align:center;">No upcoming campus events currently scheduled.</div>';
      return;
    }

    grid.innerHTML = eventRecords.map(event => {
      const registered = Number(event.is_registered) > 0;
      const capacity = event.capacity ? Number(event.capacity) : null;
      const regCount = Number(event.registrations || 0);
      const remaining = capacity === null ? null : Math.max(0, capacity - regCount);
      const isFull = remaining === 0;

      return `
        <article class="card" style="padding:22px;display:flex;flex-direction:column;justify-content:space-between;" data-search="${esc(event.title + ' ' + (event.category || '') + ' ' + (event.location || ''))}">
          <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
              <span class="badge badge-info">${esc(event.category || 'Campus')}</span>
              <span class="badge badge-neutral tabular-nums">${esc(event.event_date || 'Upcoming')}</span>
            </div>
            <h3 style="margin:0 0 8px;font-size:17px;">${esc(event.title)}</h3>
            <p style="margin:0 0 14px;color:var(--text-muted);font-size:13px;line-height:1.5;">${esc(event.description || '')}</p>
            <div style="font-size:12px;color:var(--text-muted);display:flex;flex-direction:column;gap:4px;margin-bottom:16px;">
              <span>📍 Location: <strong>${esc(event.location || 'Campus')}</strong></span>
              <span>⏰ Time: <strong>${esc(event.start_time || 'TBA')}</strong></span>
              <span>👥 Availability: <strong>${remaining === null ? 'Open enrollment' : remaining + ' places left'}</strong></span>
            </div>
          </div>
          <div>
            <button type="button" class="btn ${registered ? 'btn-secondary' : 'btn-primary'} btn-sm" style="width:100%;" data-register-event="${event.event_id}" ${registered || isFull ? 'disabled' : ''}>
              ${registered ? '✓ You are registered' : isFull ? 'Event fully booked' : 'Reserve a place →'}
            </button>
          </div>
        </article>
      `;
    }).join('');
  }

  async function loadEvents() {
    try {
      const res = await fetch(getAppPath('api/events_api.php'), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
      const data = await res.json();
      if (res.ok && data.ok && Array.isArray(data.events)) {
        eventRecords = data.events;
        eventDatabaseReady = true;
      } else {
        throw new Error('API fallback');
      }
    } catch {
      eventDatabaseReady = false;
      try {
        const fallback = await fetch(getAppPath('data/events-fallback.json'));
        eventRecords = await fallback.json();
      } catch {
        eventRecords = [];
      }
    }
    renderEventsList();
  }

  const profilePage = () => {
    const initials = (studentName.charAt(0) || 'S').toUpperCase();
    return `
      ${heading('My profile', 'Your verified student credentials and enrollment information.')}
      <div class="grid" style="grid-template-columns:300px minmax(0, 1fr);gap:24px;">
        <section class="card" style="padding:28px;text-align:center;">
          <div class="avatar" style="width:72px;height:72px;margin:0 auto 16px;font-size:24px;">${initials}</div>
          <h2 style="margin:0 0 4px;font-size:18px;">${esc(studentName)}</h2>
          <p style="margin:0 0 16px;color:var(--text-muted);font-size:13px;">${esc(student.course || 'B.Tech')}</p>
          <span class="badge badge-success">Enrolled Student</span>
        </section>

        <section class="card" style="padding:24px;">
          <h3 style="margin:0 0 16px;font-size:16px;">Enrollment record</h3>
          <div class="grid" style="grid-template-columns:1fr 1fr;gap:16px;">
            <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);">
              <small style="color:var(--text-muted);display:block;">Student ID</small>
              <strong>${esc(student.student_id || '25CS045')}</strong>
            </div>
            <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);">
              <small style="color:var(--text-muted);display:block;">Age</small>
              <strong>${esc(student.age || '20')} years</strong>
            </div>
            <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);">
              <small style="color:var(--text-muted);display:block;">Email address</small>
              <strong>${esc(student.email || 'student@example.edu')}</strong>
            </div>
            <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);">
              <small style="color:var(--text-muted);display:block;">Phone</small>
              <strong>${esc(student.phone || '+91 9876543210')}</strong>
            </div>
            <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);">
              <small style="color:var(--text-muted);display:block;">Gender</small>
              <strong>${esc(student.gender || 'Not specified')}</strong>
            </div>
            <div style="padding:10px 0;border-bottom:1px solid var(--border-subtle);">
              <small style="color:var(--text-muted);display:block;">Academic term</small>
              <strong>Fall Semester 2026–27</strong>
            </div>
          </div>
        </section>
      </div>
    `;
  };

  const pages = {
    overview,
    courses: coursesPage,
    schedule: schedulePage,
    assignments: assignmentPage,
    attendance: attendancePage,
    results: resultsPage,
    events: eventsPage,
    notices: noticesPage,
    profile: profilePage,
    help: () => {
      window.location.href = 'student_help.php';
      return '<div style="padding:48px;text-align:center;color:var(--text-muted);">Loading Help & Support Center…</div>';
    }
  };

  function render() {
    const route = (location.hash.slice(1) || 'overview').split('?')[0];
    const page = pages[route] ? route : 'overview';
    content.innerHTML = pages[page]();
    if (titleNode) titleNode.textContent = titles[page];
    document.title = `${titles[page]} · StudentHub`;

    document.querySelectorAll('.primary-nav a').forEach(a => {
      const href = a.getAttribute('href');
      const active = href === `#${page}` || href === `student_${page}.php`;
      a.classList.toggle('active', active);
    });

    if (page === 'events') {
      loadEvents();
    }
  }

  // Event handlers
  document.addEventListener('click', async (e) => {
    const registerBtn = e.target.closest('[data-register-event]');
    if (registerBtn) {
      registerBtn.disabled = true;
      registerBtn.textContent = 'Saving RSVP…';
      try {
        const body = new URLSearchParams({
          event_id: registerBtn.dataset.registerEvent,
          csrf_token: window.STUDENT_CSRF || ''
        });
        const res = await fetch(getAppPath('api/events_api.php'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
          credentials: 'same-origin',
          body
        });
        const payload = await res.json();
        eventFeedbackText = payload.message || 'Your place has been confirmed.';
      } catch {
        eventFeedbackText = 'Registration could not be completed. Please try again.';
      }
      loadEvents();
      return;
    }

    const shift = e.target.closest('[data-week-shift]');
    if (shift) {
      scheduleDate.setDate(scheduleDate.getDate() + Number(shift.dataset.weekShift) * 7);
      render();
      return;
    }

    if (e.target.closest('[data-today]')) {
      scheduleDate = new Date();
      scheduleDate.setHours(12, 0, 0, 0);
      render();
      return;
    }

    const day = e.target.closest('[data-schedule-day]');
    if (day) {
      scheduleDate = new Date(Number(day.dataset.year), Number(day.dataset.month), Number(day.dataset.day), 12);
      render();
      return;
    }

    const filterBtn = e.target.closest('[data-filter]');
    if (filterBtn) {
      document.querySelectorAll('[data-filter]').forEach(b => b.classList.toggle('active', b === filterBtn));
      const f = filterBtn.dataset.filter;
      const q = (document.getElementById('spaAssignmentSearch')?.value || '').trim().toLowerCase();
      document.querySelectorAll('#assignmentList [data-state]').forEach(card => {
        const matchesFilter = (f === 'all' || card.dataset.state === f);
        const matchesSearch = !q || (card.dataset.search || '').includes(q);
        card.style.display = (matchesFilter && matchesSearch) ? 'grid' : 'none';
      });
      return;
    }
  });

  document.addEventListener('input', (e) => {
    if (e.target.id === 'spaAssignmentSearch') {
      const q = e.target.value.trim().toLowerCase();
      const activeBtn = document.querySelector('.segmented-btn.active');
      const f = activeBtn ? activeBtn.dataset.filter : 'all';
      document.querySelectorAll('#assignmentList [data-state]').forEach(card => {
        const matchesFilter = (f === 'all' || card.dataset.state === f);
        const matchesSearch = !q || (card.dataset.search || '').includes(q);
        card.style.display = (matchesFilter && matchesSearch) ? 'grid' : 'none';
      });
    }
  });

  document.addEventListener('change', (e) => {
    const chk = e.target.closest('[data-assignment]');
    if (!chk) return;
    const id = chk.dataset.assignment;
    let done = saved().filter(x => x !== id);
    if (chk.checked) done.push(id);
    try { localStorage.setItem('studenthub-completed', JSON.stringify(done)); } catch {}

    const card = chk.closest('[data-state]');
    if (card) {
      card.dataset.state = chk.checked ? 'completed' : 'pending';
      card.classList.toggle('state-completed', chk.checked);
      if (chk.checked) {
        card.classList.remove('state-urgent');
      } else {
        const match = assignments.find(x => x.id === id);
        if (match && match.urgent) card.classList.add('state-urgent');
      }

      const title = card.querySelector('.assignment-title');
      if (title) {
        title.style.textDecoration = chk.checked ? 'line-through' : 'none';
        title.style.color = chk.checked ? 'var(--text-muted)' : 'var(--text)';
      }

      const chip = card.querySelector('.assignment-due-chip');
      if (chip) {
        if (chk.checked) {
          chip.className = 'assignment-due-chip completed';
          chip.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span class="chip-text">Completed</span>';
        } else {
          const match = assignments.find(x => x.id === id);
          if (match && match.urgent) {
            chip.className = 'assignment-due-chip urgent';
            chip.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg><span class="chip-text">Action required</span>';
          } else {
            chip.className = 'assignment-due-chip normal';
            chip.innerHTML = `<span class="chip-text">${match ? esc(match.when) : 'Pending'}</span>`;
          }
        }
      }
    }

    // Dynamic KPI updates
    const totalCount = assignments.length;
    const completedCount = assignments.filter(a => a.state !== 'Pending' || done.includes(a.id)).length;
    const pendingCount = totalCount - completedCount;
    const rate = Math.round((completedCount / totalCount) * 100);

    const kpiPending = document.getElementById('spaKpiPending');
    if (kpiPending) kpiPending.textContent = pendingCount;
    const kpiCompleted = document.getElementById('spaKpiCompleted');
    if (kpiCompleted) kpiCompleted.textContent = completedCount;
    const kpiRate = document.getElementById('spaKpiRate');
    if (kpiRate) kpiRate.textContent = rate + '% completion rate';

    const countPending = document.getElementById('spaCountPending');
    if (countPending) countPending.textContent = pendingCount;
    const countCompleted = document.getElementById('spaCountCompleted');
    if (countCompleted) countCompleted.textContent = completedCount;

    const badgeText = document.getElementById('spaPendingBadgeText');
    if (badgeText) badgeText.textContent = pendingCount + ' Pending deliverables';

    const progText = document.getElementById('spaProgressMetricText');
    if (progText) progText.textContent = `${completedCount} of ${totalCount} deliverables completed (${rate}%)`;

    const fillEl = document.getElementById('spaProgressBarFill');
    if (fillEl) fillEl.style.width = rate + '%';
  });

  window.addEventListener('hashchange', render);
  render();
})();

