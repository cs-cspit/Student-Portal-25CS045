(() => {
  const content = document.getElementById('pageContent');
  const nav = document.getElementById('primaryNav');
  const titleNode = document.getElementById('pageCrumb');
  const search = document.getElementById('pageSearch');
  const titles = {overview:'Overview',courses:'My courses',schedule:'Schedule',assignments:'Assignments',attendance:'Attendance',results:'Results',events:'Events',notices:'Notices',profile:'My profile'};
  const courses = [
    {name:'Data Structures & Algorithms',code:'CS-201',faculty:'Dr. Rajesh Patel',attendance:92,work:'2 of 4 assignments submitted',color:'teal'},
    {name:'Database Systems',code:'CS-305',faculty:'Prof. Neha Shah',attendance:89,work:'1 of 3 assignments submitted',color:'blue'},
    {name:'Discrete Mathematics',code:'MATH-102',faculty:'Dr. Kavita Mehta',attendance:94,work:'All assignments submitted',color:'gold'},
    {name:'Operating Systems',code:'CS-204',faculty:'Prof. Arjun Desai',attendance:78,work:'1 of 4 assignments submitted',color:'rose'},
    {name:'Ethics in Technology',code:'HUM-201',faculty:'Dr. Priya Iyer',attendance:100,work:'All assignments submitted',color:'teal'}
  ];
  const assignments = [
    {id:'a1',title:'Binary Search Trees — problem set',code:'CS-201',due:'02 Nov 2026',when:'Due in 4 days',state:'Pending'},
    {id:'a2',title:'SQL Normalization — project brief',code:'CS-305',due:'04 Nov 2026',when:'Due in 6 days',state:'Pending'},
    {id:'a3',title:'Operating Systems — process worksheet',code:'CS-204',due:'06 Nov 2026',when:'Due in 8 days',state:'Pending'},
    {id:'a4',title:'Discrete Mathematics — proof exercises',code:'MATH-102',due:'28 Oct 2026',when:'Submitted',state:'Submitted'},
    {id:'a5',title:'Ethics in Technology — case response',code:'HUM-201',due:'24 Oct 2026',when:'Graded · 92 / 100',state:'Completed'}
  ];
  const weekdaySchedules = {
    1:[['09:00','Data Structures & Algorithms','CS-201 · Lecture Hall 3',''],['11:00','Database Systems','CS-305 · Lab 1','gold'],['14:00','Discrete Mathematics','MATH-102 · Room 204','blue']],
    2:[['10:00','Operating Systems','CS-204 · Lab 3',''],['13:00','Independent study','Library · Level 2','blue'],['15:00','Data Structures tutorial','CS-201 · Room 3','gold']],
    3:[['09:00','Database Systems lab','CS-305 · Lab 1','gold'],['11:00','Ethics in Technology','HUM-201 · Room 105','blue'],['14:00','Discrete Mathematics tutorial','MATH-102 · Room 204','']],
    4:[['09:00','Data Structures & Algorithms','CS-201 · Lecture Hall 3',''],['10:00','Independent study','Library · Level 2','blue'],['11:00','Database Systems','CS-305 · Lab 1','gold'],['13:00','Discrete Mathematics','MATH-102 · Room 204','blue'],['14:30','Operating Systems','CS-204 · Lab 3','']],
    5:[['09:00','Operating Systems lab','CS-204 · Lab 3',''],['11:00','Project work','Computer Lab · Room 2','gold'],['14:00','Academic advising','Faculty office','blue']]
  };
  let scheduleDate = new Date();
  scheduleDate.setHours(12,0,0,0);
  const dailySchedule = date => weekdaySchedules[date.getDay()] || [];
  const saved = () => { try { return JSON.parse(localStorage.getItem('studenthub-completed') || '[]'); } catch { return []; } };
  const esc = (value) => String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const heading = (title, description, action='') => `<div class="page-heading"><div><p class="eyebrow">Student workspace</p><h1>${title}</h1><p>${description}</p></div>${action ? `<div class="heading-actions">${action}</div>` : ''}</div>`;
  const card = (title, body, link='') => `<section class="card"><div class="card-header"><h2>${title}</h2>${link}</div><div class="card-body">${body}</div></section>`;
  const progress = (label, value, warn=false) => `<div class="progress-item"><strong>${label}</strong><span>${value}%</span><div class="progress-track"><i class="${warn?'warn':''}" style="width:${value}%"></i></div></div>`;
  const tasksHtml = (limit=4) => assignments.filter(x=>x.state==='Pending').slice(0,limit).map(x=>`<div class="task-row" data-search="${esc(x.title+' '+x.code)}"><span class="task-mark"></span><span class="task-main"><strong>${esc(x.title)}</strong><small>${esc(x.code)} · ${esc(x.when)}</small></span><span class="task-deadline">${esc(x.due)}</span></div>`).join('');
  const scheduleHtml = (list=dailySchedule(new Date()).slice(0,4)) => `<div class="schedule-list">${list.length?list.map((x,i)=>`<div class="schedule-row" data-search="${esc(x.join(' '))}"><span class="schedule-time">${x[0]}</span><span class="schedule-course ${x[3]}"><strong>${esc(x[1])}</strong><small>${esc(x[2])}</small></span><span class="schedule-room">${i===0?'Today':''}</span></div>`).join(''):'<p class="empty-state">No classes are scheduled for today.</p>'}</div>`;
  const courseCards = () => `<div class="grid course-grid">${courses.map(c=>`<article class="card course-card" data-search="${esc(c.name+' '+c.code+' '+c.faculty)}"><div class="course-card-top"><span class="course-code-badge">${c.code}</span><span class="status-pill ${c.attendance<80?'warning':''}">${c.attendance<80?'Review attendance':'Active'}</span></div><h3>${esc(c.name)}</h3><p>${esc(c.faculty)}</p><div class="course-meta"><span>Attendance</span><strong>${c.attendance}%</strong></div><div class="progress-track"><i class="${c.attendance<80?'warn':''}" style="width:${c.attendance}%"></i></div><div class="course-meta"><span>${esc(c.work)}</span><a class="text-link" href="#schedule">Schedule →</a></div></article>`).join('')}</div>`;
  const upcomingPanel = card('Upcoming assignments', '<div class="task-list">'+tasksHtml(3)+'</div>', '<a class="text-link" href="#assignments">View all →</a>');
  const todayPanel = card('Today’s schedule', scheduleHtml(), '<a class="text-link" href="#schedule">Full schedule →</a>');
  const quickPanel = card('Quick access', '<div class="quick-grid"><a class="quick-link" href="#courses"><span>▤</span>My courses</a><a class="quick-link" href="#attendance"><span>◷</span>Attendance</a><a class="quick-link" href="#results"><span>▥</span>Results</a></div>');
  const attendancePanel = card('Course attendance', '<div class="progress-list">'+courses.slice(0,4).map(c=>progress(c.code,c.attendance,c.attendance<80)).join('')+'</div>', '<a class="text-link" href="#attendance">Details →</a>');
  const noticesPanel = card('Latest notices', '<div class="notice-item"><span class="notice-type important">Academic</span><strong>Mid-semester assessment schedule</strong><p>Updated by the academic office · 28 Oct</p></div><div class="notice-item"><span class="notice-type">Library</span><strong>Study hall hours extended</strong><p>Reading room open until 10:00 PM</p></div>', '<a class="text-link" href="#notices">All notices →</a>');
  const overview = () => `${heading('Academic overview','Here is your academic overview for the current semester.')}<section class="welcome-card"><h2>Your semester, in focus</h2><p>Review upcoming deadlines, today’s classes, and course progress from one workspace.</p><a class="welcome-action" href="#assignments">Review assignments <span aria-hidden="true">→</span></a></section><div class="grid stats-grid"><article class="card stat-card"><div class="stat-top">Attendance <span class="stat-symbol">◷</span></div><strong class="stat-value">90.3%</strong><span class="stat-note"><span class="positive">Above</span> 75% requirement</span></article><article class="card stat-card"><div class="stat-top">Current GPA <span class="stat-symbol">▥</span></div><strong class="stat-value">8.77<span style="font-size:13px;color:#8995a4"> / 10</span></strong><span class="stat-note">Based on sample results</span></article><article class="card stat-card"><div class="stat-top">Courses enrolled <span class="stat-symbol">▤</span></div><strong class="stat-value">05</strong><span class="stat-note">Fall semester 2026–27</span></article><article class="card stat-card"><div class="stat-top">Due this week <span class="stat-symbol">✓</span></div><strong class="stat-value">03</strong><span class="stat-note"><span class="attention">Next due Nov 02</span></span></article></div><div class="content-grid"><div class="column-stack">${upcomingPanel}${todayPanel}${quickPanel}</div><div class="column-stack">${attendancePanel}${noticesPanel}</div></div><p class="demo-note">This project preview uses sample academic information. Connect an authorized student system before using it for real records.</p>`;
  const coursesPage = () => `${heading('My courses','Your enrolled courses, instructors, and progress.','<button class="button" type="button" data-action="print">↧ <span>Print course list</span></button>')}<div class="section-toolbar"><span class="eyebrow" style="margin:0">Fall semester · 5 courses</span><span class="status-pill neutral">Current term</span></div>${courseCards()}<p class="demo-note">Course and instructor details shown here are sample records for the project prototype.</p>`;
  const schedulePage = () => {
    const monday = new Date(scheduleDate);
    monday.setDate(monday.getDate() - ((monday.getDay() + 6) % 7));
    const days = Array.from({length:5},(_,i)=>{const d=new Date(monday);d.setDate(monday.getDate()+i);return d;});
    const format = (date, options) => new Intl.DateTimeFormat('en',options).format(date);
    const actions = '<button class="button" type="button" data-week-shift="-1" aria-label="Previous week">←</button><button class="button" type="button" data-today>Today</button><button class="button" type="button" data-week-shift="1" aria-label="Next week">→</button><button class="button" type="button" data-action="print">Print</button>';
    const dayButtons = days.map(day=>`<button class="day-chip ${day.toDateString()===scheduleDate.toDateString()?'selected':''}" type="button" data-schedule-day data-year="${day.getFullYear()}" data-month="${day.getMonth()}" data-day="${day.getDate()}" aria-pressed="${day.toDateString()===scheduleDate.toDateString()}"><span>${format(day,{weekday:'short'}).toUpperCase()}</span><strong>${day.getDate()}</strong></button>`).join('');
    const rows = dailySchedule(scheduleDate);
    const blocks = rows.length ? rows.map(x=>`<div class="schedule-block" data-search="${esc(x.join(' '))}"><time>${esc(x[0])}</time><div class="class-card ${x[3]}"><strong>${esc(x[1])}</strong><small>${esc(x[2])} · Sample timetable</small></div></div>`).join('') : '<div class="empty-state">No classes are scheduled for this day.</div>';
    return heading('Class schedule','Browse the week and select a day to view its timetable.',actions) + `<section class="card panel"><div class="week-strip">${dayButtons}</div><h2>${format(scheduleDate,{weekday:'long',day:'numeric',month:'long',year:'numeric'})}</h2>${blocks}</section><p class="demo-note">Class times are sample content. Check your college timetable for official room and schedule updates.</p>`;
  };
  const assignmentPage = () => { const done=saved(); const items=assignments.map(a=>{const isDone=a.state!=='Pending'||done.includes(a.id);return `<article class="card assignment-card" data-search="${esc(a.title+' '+a.code+' '+a.state)}" data-state="${isDone?'completed':'pending'}"><input class="assignment-check" type="checkbox" data-assignment="${a.id}" ${isDone?'checked':''} aria-label="Mark ${esc(a.title)} complete"><div><h3>${esc(a.title)}</h3><p>${esc(a.code)} · ${a.state==='Pending'?'Submission required':esc(a.state)}</p></div><div class="assignment-side"><strong>${isDone?'Complete':esc(a.when)}</strong><small>${esc(a.due)}</small></div></article>`}).join('');return `${heading('Assignments','Track deadlines and mark work complete as you progress.')}<div class="section-toolbar"><div class="filter-tabs" role="group" aria-label="Filter assignments"><button class="active" data-filter="all">All</button><button data-filter="pending">To do</button><button data-filter="completed">Completed</button></div><span class="eyebrow" style="margin:0">5 items</span></div><div id="assignmentList">${items}</div><p class="demo-note">Completion changes are saved in this browser only. File uploads and official submissions are not connected.</p>`; };
  const attendancePage = () => `${heading('Attendance','Review attendance by course and identify classes that need attention.')}<div class="grid summary-grid"><article class="card summary-box"><span>Overall attendance</span><strong>90.3%</strong></article><article class="card summary-box"><span>Classes attended</span><strong>159 / 176</strong></article><article class="card summary-box"><span>Below 80% threshold</span><strong>01 course</strong></article></div><section class="card"><div class="card-header"><div><h2>Course attendance</h2><p>Sample record · Fall semester 2026–27</p></div></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Course</th><th>Classes held</th><th>Attended</th><th>Attendance</th><th>Status</th></tr></thead><tbody>${courses.map(c=>`<tr data-search="${esc(c.name+' '+c.code)}"><td>${esc(c.name)}<span class="course-code">${c.code}</span></td><td>${c.code==='CS-201'?32:36}</td><td>${Math.round((c.code==='CS-201'?32:36)*c.attendance/100)}</td><td>${c.attendance}%</td><td><span class="status-pill ${c.attendance<80?'warning':''}">${c.attendance<80?'Needs attention':'On track'}</span></td></tr>`).join('')}</tbody></table></div></section><p class="demo-note">Attendance figures are illustrative. Confirm official attendance with your institution.</p>`;
  const resultsPage = () => `${heading('Results','A semester summary of grades and academic progress.','<button class="button" type="button" data-action="print">↧ <span>Print summary</span></button>')}<div class="result-highlight"><div><span>Cumulative grade point average</span><strong>8.77 / 10.00</strong></div><div class="credits"><span>Credits completed</span><strong>42 / 120</strong></div></div><section class="card" style="margin-top:15px"><div class="card-header"><div><h2>Semester results</h2><p>Spring semester 2026 · Sample academic record</p></div><span class="status-pill neutral">Unofficial preview</span></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Course</th><th>Credits</th><th>Coursework</th><th>Final</th><th>Grade</th><th>Grade points</th></tr></thead><tbody><tr><td>Data Structures & Algorithms<span class="course-code">CS-201</span></td><td>4</td><td>92%</td><td>94%</td><td>A</td><td>9.0</td></tr><tr><td>Database Systems<span class="course-code">CS-305</span></td><td>3</td><td>88%</td><td>90%</td><td>A−</td><td>8.5</td></tr><tr><td>Operating Systems<span class="course-code">CS-204</span></td><td>4</td><td>82%</td><td>85%</td><td>B+</td><td>8.0</td></tr><tr><td>Discrete Mathematics<span class="course-code">MATH-102</span></td><td>4</td><td>96%</td><td>98%</td><td>A+</td><td>9.5</td></tr></tbody></table></div></section><p class="demo-note">Results are sample content and are not an official transcript.</p>`;
  const noticesPage = () => `${heading('Notices','Academic and campus updates relevant to your semester.')}<div class="notice-list"><article class="card notice-full"><span class="notice-icon">!</span><div><span class="notice-type important">Academic office</span><h3>Mid-semester assessment schedule published</h3><p>The assessment timetable is available for review. Confirm room details with your department before each examination.</p><time datetime="2026-10-28">28 October 2026</time></div></article><article class="card notice-full"><span class="notice-icon">▤</span><div><span class="notice-type">Library services</span><h3>Extended reading room hours</h3><p>The central reading room will remain open until 10:00 PM during the assessment period.</p><time datetime="2026-10-26">26 October 2026</time></div></article><article class="card notice-full"><span class="notice-icon">◷</span><div><span class="notice-type">Department</span><h3>Course advising appointments</h3><p>Academic advising appointments can be requested through the department office this week.</p><time datetime="2026-10-24">24 October 2026</time></div></article></div><p class="demo-note">Notices shown are examples for this student portal prototype.</p>`;
  let eventRecords = [];
  let eventDatabaseReady = false;
  let eventFeedbackText = '';
  let eventLoadVersion = 0;
  const eventsPage = () => `${heading('Events & opportunities','Explore workshops, campus programs, research sessions, and career events.')}<section class="events-hero"><div class="events-hero-copy"><p class="eyebrow">CAMPUS CALENDAR</p><h2>Find your next<br><span>opportunity.</span></h2><p>Learn with your peers, meet new people, and make room for ideas beyond the classroom.</p><a href="#event-list" class="events-hero-link">Browse upcoming events <span aria-hidden="true">↓</span></a></div><div class="events-hero-art" aria-hidden="true"><span class="events-orbit events-orbit-one"></span><span class="events-orbit events-orbit-two"></span><span class="events-art-mark">✳</span><span class="events-art-label">STUDENT LIFE<br>2026 — 27</span><span class="events-art-dot"></span></div></section><div class="events-summary"><div><strong id="eventsTotal">—</strong><span> upcoming events</span></div><p>Choose a program and reserve your place online.</p></div><div class="events-toolbar"><div class="filter-tabs event-filters" id="eventFilters" role="group" aria-label="Filter events"><button class="active" type="button" data-event-filter="all" aria-pressed="true">All events</button></div><span class="events-updated" id="eventsSource">Loading event calendar…</span></div><p class="event-feedback" id="eventFeedback" aria-live="polite" role="status"></p><section class="events-grid" id="event-list" aria-label="Upcoming events"><article class="event-skeleton" aria-hidden="true"></article><article class="event-skeleton" aria-hidden="true"></article><article class="event-skeleton" aria-hidden="true"></article></section><p class="demo-note">Event listings are sample campus information. Confirm dates and venue details with the event organiser.</p>`;
  const eventTheme = category => ({Workshop:'mint',Research:'blue',Career:'gold',Campus:'violet',Seminar:'blue'})[category] || 'mint';
  const eventIcon = category => ({Workshop:'✳',Research:'⌕',Career:'↗',Campus:'◈',Seminar:'◎'})[category] || '✳';
  function renderEvents() {
    const grid = content.querySelector('#event-list');
    if (!grid) return;
    const filters = content.querySelector('#eventFilters');
    const total = content.querySelector('#eventsTotal');
    const source = content.querySelector('#eventsSource');
    const feedback = content.querySelector('#eventFeedback');
    if (total) total.textContent = String(eventRecords.length).padStart(2, '0');
    if (source) source.textContent = eventDatabaseReady ? 'Live campus calendar' : 'Local event preview';
    if (feedback) {
      feedback.textContent = eventFeedbackText || (eventDatabaseReady ? '' : 'Connect the local database to enable event registration. The preview below remains available.');
      feedback.classList.toggle('event-feedback-visible', Boolean(feedback.textContent));
    }
    if (filters) {
      const categories = [...new Set(eventRecords.map(event => String(event.category || 'Other')))].sort();
      filters.innerHTML = `<button class="active" type="button" data-event-filter="all" aria-pressed="true">All events</button>${categories.map(category => `<button type="button" data-event-filter="${esc(category)}" aria-pressed="false">${esc(category)}</button>`).join('')}`;
    }
    if (!eventRecords.length) {
      grid.innerHTML = '<div class="event-empty"><span aria-hidden="true">◷</span><h2>No upcoming events</h2><p>New campus events will appear here as soon as they are published.</p></div>';
      return;
    }
    const formatter = new Intl.DateTimeFormat('en', {month:'short',day:'2-digit'});
    const weekdayFormatter = new Intl.DateTimeFormat('en', {weekday:'short'});
    grid.innerHTML = eventRecords.map((event,index) => {
      const date = new Date(`${String(event.event_date).slice(0,10)}T12:00:00`);
      const registered = Number(event.is_registered) > 0;
      const capacity = event.capacity === null || event.capacity === '' ? null : Number(event.capacity);
      const remaining = capacity === null ? null : Math.max(0, capacity - Number(event.registrations || 0));
      const soldOut = remaining === 0;
      const unavailable = !eventDatabaseReady;
      const buttonLabel = registered ? 'You are registered' : soldOut ? 'Fully booked' : unavailable ? 'Registration unavailable' : 'Reserve a place';
      const disabled = registered || soldOut || unavailable;
      const when = event.start_time ? new Intl.DateTimeFormat('en', {hour:'numeric',minute:'2-digit'}).format(new Date(`2000-01-01T${event.start_time}`)) : 'Time to be confirmed';
      const remainingLabel = remaining === null ? 'Open registration' : `${remaining} places remaining`;
      return `<article class="event-card event-theme-${eventTheme(event.category)}" data-search="${esc(`${event.title} ${event.category} ${event.location} ${event.description}`)}" data-event-category="${esc(event.category || 'Other')}" style="--event-delay:${Math.min(index,5)*65}ms"><div class="event-card-art"><span class="event-category">${esc(event.category || 'Campus')}</span><span class="event-card-symbol" aria-hidden="true">${eventIcon(event.category)}</span><div class="event-date-tile"><span>${weekdayFormatter.format(date)}</span><strong>${formatter.format(date)}</strong></div></div><div class="event-card-content"><div class="event-card-title-row"><h2>${esc(event.title)}</h2><span class="event-arrow" aria-hidden="true">↗</span></div><p class="event-description">${esc(event.description)}</p><div class="event-meta"><span><i aria-hidden="true">◷</i>${esc(when)}</span><span><i aria-hidden="true">⌖</i>${esc(event.location)}</span></div><div class="event-card-footer"><span class="event-capacity">${esc(remainingLabel)}</span><button type="button" class="event-register ${registered?'is-registered':''}" data-register-event="${Number(event.event_id)}" ${disabled?'disabled':''}>${esc(buttonLabel)} <span aria-hidden="true">${registered?'✓':'→'}</span></button></div></div></article>`;
    }).join('');
    applySearch();
  }
  async function loadEvents() {
    const requestVersion = ++eventLoadVersion;
    try {
      const response = await fetch('events_api.php', {headers:{Accept:'application/json'}, credentials:'same-origin'});
      const payload = await response.json();
      if (!response.ok || !payload.ok || !Array.isArray(payload.events)) throw new Error('Event database unavailable');
      eventRecords = payload.events;
      eventDatabaseReady = true;
    } catch {
      eventDatabaseReady = false;
      try {
        const response = await fetch('data/events-fallback.json', {headers:{Accept:'application/json'}, credentials:'same-origin'});
        if (!response.ok) throw new Error('Event preview unavailable');
        eventRecords = await response.json();
      } catch {
        eventRecords = [];
      }
    }
    if (requestVersion === eventLoadVersion && location.hash.slice(1) === 'events') renderEvents();
  }
  const profilePage = () => {
    const student=window.STUDENT_PROFILE||{};
    const name=esc(student.name||'Student');
    const initials=esc(name.split(/\s+/).map(part=>part.charAt(0)).slice(0,2).join('').toUpperCase());
    const fields=[['Student ID',student.student_id],['Age',student.age],['College or registered email',student.email],['Phone number',student.phone],['Gender',student.gender],['Enrolled course',student.course]];
    return `${heading('My profile','Your student account information.')}<div class="profile-layout"><section class="card profile-card"><span class="avatar avatar-large">${initials}</span><h2>${name}</h2><p>Registered StudentHub account</p><span class="status-pill">Account active</span><form class="profile-logout" method="post" action="logout.php"><input type="hidden" name="csrf_token" value="${esc(window.STUDENT_CSRF||'')}"><button class="button" type="submit">Log out</button></form></section><section class="card profile-details"><h2>Student details</h2><div class="detail-grid">${fields.map(([label,value])=>`<div class="detail-item"><span>${esc(label)}</span><strong>${esc(value||'Not provided')}</strong></div>`).join('')}</div></section></div><p class="demo-note">Class, attendance, and result screens contain sample academic content until connected to your college system.</p>`;
  };
  const pages = {overview,courses:coursesPage,schedule:schedulePage,assignments:assignmentPage,attendance:attendancePage,results:resultsPage,events:eventsPage,notices:noticesPage,profile:profilePage};
  function render(){const route=(location.hash.slice(1)||'overview').split('?')[0];const page=pages[route]?route:'overview';if(!pages[route]&&location.hash)history.replaceState(null,'','#overview');content.innerHTML=pages[page]();titleNode.textContent=titles[page];document.title=`${titles[page]} · StudentHub`;document.querySelectorAll('[data-route]').forEach(a=>{const active=a.dataset.route===page;a.classList.toggle('active',active);if(active)a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');});applySearch();if(page==='events')loadEvents();}
  function applySearch(){const q=search.value.trim().toLocaleLowerCase();content.querySelectorAll('[data-search]').forEach(el=>el.classList.toggle('is-filtered-out',q!==''&&!el.dataset.search.toLocaleLowerCase().includes(q)));}
  document.addEventListener('click',e=>{
    const eventFilter=e.target.closest('[data-event-filter]');
    if(eventFilter){content.querySelectorAll('[data-event-filter]').forEach(button=>{const active=button===eventFilter;button.classList.toggle('active',active);button.setAttribute('aria-pressed',String(active));});content.querySelectorAll('[data-event-category]').forEach(card=>card.classList.toggle('is-filtered-out',eventFilter.dataset.eventFilter!=='all'&&card.dataset.eventCategory!==eventFilter.dataset.eventFilter));return;}
    const registerEvent=e.target.closest('[data-register-event]');
    if(registerEvent){submitEventRegistration(registerEvent);return;}
    const shift=e.target.closest('[data-week-shift]');
    if(shift){scheduleDate.setDate(scheduleDate.getDate()+Number(shift.dataset.weekShift)*7);render();return;}
    if(e.target.closest('[data-today]')){scheduleDate=new Date();scheduleDate.setHours(12,0,0,0);render();return;}
    const day=e.target.closest('[data-schedule-day]');
    if(day){scheduleDate=new Date(Number(day.dataset.year),Number(day.dataset.month),Number(day.dataset.day),12);render();return;}
    const filter=e.target.closest('[data-filter]');
    if(filter){document.querySelectorAll('[data-filter]').forEach(b=>b.classList.toggle('active',b===filter));document.querySelectorAll('#assignmentList [data-state]').forEach(row=>row.classList.toggle('is-filtered-out',filter.dataset.filter!=='all'&&row.dataset.state!==filter.dataset.filter));}
    const print=e.target.closest('[data-action="print"]');
    if(print)window.print();
    const link=e.target.closest('a[data-route]');
    if(link)closeMenu();
  });
  async function submitEventRegistration(button){
    button.disabled=true;button.innerHTML='Saving your place…';eventFeedbackText='';
    try{
      const body=new URLSearchParams({event_id:button.dataset.registerEvent,csrf_token:window.STUDENT_CSRF||''});
      const response=await fetch('events_api.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','Accept':'application/json'},credentials:'same-origin',body});
      const payload=await response.json();
      if(!response.ok||!payload.ok)throw new Error(payload.message||'Registration could not be completed.');
      eventFeedbackText=payload.message||'Your place is confirmed.';
      await loadEvents();
    }catch(error){
      eventFeedbackText=error instanceof Error?error.message:'Registration could not be completed. Please try again.';
      renderEvents();
    }
  }
  document.addEventListener('change',e=>{const input=e.target.closest('[data-assignment]');if(!input)return;const done=saved().filter(id=>id!==input.dataset.assignment);if(input.checked)done.push(input.dataset.assignment);try{localStorage.setItem('studenthub-completed',JSON.stringify(done));}catch{}const row=input.closest('.assignment-card');row.dataset.state=input.checked?'completed':'pending';row.querySelector('.assignment-side strong').textContent=input.checked?'Complete':assignments.find(x=>x.id===input.dataset.assignment).when;});
  search.addEventListener('input',applySearch);window.addEventListener('hashchange',render);
  const sidebar=document.getElementById('sidebar'),scrim=document.getElementById('mobileScrim'),toggle=document.getElementById('menuToggle');
  function closeMenu(){sidebar.classList.remove('open');scrim.classList.remove('show');toggle.setAttribute('aria-expanded','false');}
  toggle.addEventListener('click',()=>{const open=sidebar.classList.toggle('open');scrim.classList.toggle('show',open);toggle.setAttribute('aria-expanded',String(open));});scrim.addEventListener('click',closeMenu);
  document.addEventListener('keydown',e=>{if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==='k'){e.preventDefault();search.focus();}if(e.key==='Escape')closeMenu();});
  render();
})();



