<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$student = $_SESSION['student'] ?? [];
$studentName = (string)($student['name'] ?? $student['full_name'] ?? 'Student');
$studentId = (string)($student['student_id'] ?? '25CS045');
$studentEmail = (string)($student['email'] ?? 'student@charusat.edu.in');
$studentPhone = (string)($student['phone'] ?? '+91 98765 43210');

$pageTitle = 'Help & Support Center';
$pageCrumb = 'Help Center';
$activeRoute = 'help';
$portalType = 'student';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_help_ticket') {
    if (!app_csrf_is_valid()) {
        $errorMessage = 'Your security session expired. Please refresh the page and try again.';
    } else {
        $category = trim((string)($_POST['category'] ?? 'General Inquiry'));
        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));
        $priority = trim((string)($_POST['priority'] ?? 'Normal'));

        if ($subject === '' || $message === '') {
            $errorMessage = 'Please provide both a subject line and a detailed description of your request.';
        } else {
            $ticketCode = app_save_help_request([
                'name' => $studentName,
                'student_id' => $studentId,
                'email' => $studentEmail,
                'phone' => $studentPhone,
                'category' => $category,
                'subject' => $subject,
                'message' => $message,
                'priority' => $priority,
                'source' => 'Student Portal',
            ]);
            $successMessage = 'Help request submitted successfully! Your tracking reference code is ' . $ticketCode . '. An academic counselor will review it shortly.';
        }
    }
}

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">

      <?php if ($successMessage !== ''): ?>
        <div class="alert alert-success" role="status" style="margin-bottom:24px;border-left:4px solid var(--success);display:flex;align-items:center;gap:12px;padding:16px 20px;border-radius:12px;background:var(--success-subtle);color:var(--text);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success);flex-shrink:0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          <div>
            <strong style="display:block;font-size:15px;color:var(--success);">Request Lodged Successfully</strong>
            <span style="font-size:14px;"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($errorMessage !== ''): ?>
        <div class="alert alert-error" role="alert" style="margin-bottom:24px;border-left:4px solid var(--error);display:flex;align-items:center;gap:12px;padding:16px 20px;border-radius:12px;background:var(--error-subtle);color:var(--text);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--error);flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <div>
            <strong style="display:block;font-size:15px;color:var(--error);">Unable to Process</strong>
            <span style="font-size:14px;"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
      <?php endif; ?>

      <!-- HERO BANNER (Matches user screenshot: Deep Indigo Rounded Card) -->
      <section class="help-hero-banner" style="background: linear-gradient(135deg, #2b2585 0%, #3730a3 40%, #4338ca 100%); color: #ffffff; border-radius: 20px; padding: 44px 32px; text-align: center; margin-bottom: 32px; position: relative; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(55, 48, 163, 0.3);">
        <div style="position:relative;z-index:2;max-width:720px;margin:0 auto;">
          <img src="<?= app_base_url() ?>images/charusat-logo.png" alt="CHARUSAT University Logo" style="height:46px;width:auto;object-fit:contain;margin:0 auto 16px;display:block;filter:drop-shadow(0 2px 8px rgba(0,0,0,0.3)) brightness(1.2);">
          <h1 style="font-size:36px;font-weight:700;color:#ffffff;margin:0 0 12px;letter-spacing:-0.5px;">How can we help you?</h1>
          <p style="font-size:15px;color:#c7d2fe;max-width:580px;margin:0 auto 28px;line-height:1.6;">
            Search our knowledge base or browse through common topics below to find the answers you need.
          </p>

          <!-- Search Bar -->
          <div style="display:flex;align-items:center;background:#ffffff;border-radius:50px;padding:6px 8px 6px 20px;box-shadow:0 6px 20px rgba(0,0,0,0.18);max-width:620px;margin:0 auto;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" style="margin-right:12px;flex-shrink:0;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="search" id="helpIssueSearch" placeholder="Describe your issue (e.g. 'How to reset password')" style="flex:1;border:none;outline:none;font-size:15px;color:#1f2937;background:transparent;padding:6px 0;" aria-label="Search help topics">
            <button type="button" id="helpSearchBtn" style="background:#3730a3;color:#ffffff;border:none;border-radius:50px;padding:10px 26px;font-size:14px;font-weight:600;cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#2b2585'" onmouseout="this.style.background='#3730a3'">
              Search
            </button>
          </div>
        </div>
      </section>

      <!-- 6 TOPIC CATEGORY CARDS (Matches user screenshot: 3 columns x 2 rows) -->
      <section aria-labelledby="topics-heading" style="margin-bottom:32px;">
        <h2 id="topics-heading" class="sr-only">Help Categories</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:20px;" id="topicsGrid">
          
          <!-- Card 1: Login & Access -->
          <div class="card help-topic-card" data-category="Login & Access" data-keywords="login password reset access credential 2fa account authentication" style="padding:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;gap:14px;" onclick="openTopicModal('Login & Access')">
            <div style="width:44px;height:44px;border-radius:12px;background:#EEF2FF;display:grid;place-items:center;color:#4F46E5;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            </div>
            <div>
              <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0 0 6px;">Login & Access</h3>
              <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">
                Troubleshooting login issues, password resets, and multi-factor authentication setup.
              </p>
            </div>
          </div>

          <!-- Card 2: Courses & Registration -->
          <div class="card help-topic-card" data-category="Courses & Registration" data-keywords="course registration syllabus enrollment faculty professor elective" style="padding:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;gap:14px;" onclick="openTopicModal('Courses & Registration')">
            <div style="width:44px;height:44px;border-radius:12px;background:#ECFDF5;display:grid;place-items:center;color:#10B981;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </div>
            <div>
              <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0 0 6px;">Courses & Registration</h3>
              <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">
                Information on course enrollment, drop/add periods, and accessing course materials.
              </p>
            </div>
          </div>

          <!-- Card 3: Attendance & Schedule -->
          <div class="card help-topic-card" data-category="Attendance & Schedule" data-keywords="attendance schedule timetable lecture lab leave percentage 75" style="padding:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;gap:14px;" onclick="openTopicModal('Attendance & Schedule')">
            <div style="width:44px;height:44px;border-radius:12px;background:#FFFBEB;display:grid;place-items:center;color:#F59E0B;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><path d="M9 16l2 2 4-4"></path></svg>
            </div>
            <div>
              <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0 0 6px;">Attendance & Schedule</h3>
              <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">
                How to track your attendance, request leaves, and manage your weekly timetable.
              </p>
            </div>
          </div>

          <!-- Card 4: Assignments & Results -->
          <div class="card help-topic-card" data-category="Assignments & Results" data-keywords="assignment result grades marks submission deadline sgpa cgpa transcript" style="padding:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;gap:14px;" onclick="openTopicModal('Assignments & Results')">
            <div style="width:44px;height:44px;border-radius:12px;background:#F5F3FF;display:grid;place-items:center;color:#7C3AED;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <div>
              <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0 0 6px;">Assignments & Results</h3>
              <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">
                Submission guidelines, checking grades, and understanding GPA calculations.
              </p>
            </div>
          </div>

          <!-- Card 5: Account Settings -->
          <div class="card help-topic-card" data-category="Account Settings" data-keywords="account settings profile email phone password privacy notification" style="padding:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;gap:14px;" onclick="openTopicModal('Account Settings')">
            <div style="width:44px;height:44px;border-radius:12px;background:#ECFEFF;display:grid;place-items:center;color:#06B6D4;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><circle cx="19" cy="8" r="2"></circle><line x1="19" y1="10" x2="19" y2="12"></line></svg>
            </div>
            <div>
              <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0 0 6px;">Account Settings</h3>
              <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">
                Updating profile information, managing privacy settings, and email preferences.
              </p>
            </div>
          </div>

          <!-- Card 6: FAQ Access -->
          <div class="card help-topic-card" data-category="FAQ Access" data-keywords="faq frequently questions help guide policy handbook library exam" style="padding:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;gap:14px;" onclick="openTopicModal('FAQ Access')">
            <div style="width:44px;height:44px;border-radius:12px;background:#FFF7ED;display:grid;place-items:center;color:#EA580C;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <div>
              <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0 0 6px;">FAQ Access</h3>
              <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">
                Quick answers to frequently asked questions from the student community.
              </p>
            </div>
          </div>

        </div>
      </section>

      <!-- STILL NEED HELP? SECTION CARD (Matches user screenshot: Bottom Support Card) -->
      <section class="card" style="border-radius:18px;background:var(--surface);border:1px solid var(--border);padding:32px 36px;margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:28px;flex-wrap:wrap;">
          
          <!-- Headset Icon in Circle Badge -->
          <div style="width:72px;height:72px;border-radius:50%;background:#EEF2FF;display:grid;place-items:center;color:#3730A3;flex-shrink:0;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
          </div>

          <!-- Description and Buttons -->
          <div style="flex:1;min-width:260px;">
            <h3 style="font-size:22px;font-weight:700;color:var(--text);margin:0 0 8px;">Still need help?</h3>
            <p style="font-size:14px;color:var(--text-muted);margin:0 0 20px;line-height:1.6;">
              Our support team is available Monday to Friday, 9:00 AM to 6:00 PM. We typically respond within 24 hours.
            </p>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
              <button type="button" class="btn btn-primary" id="openSupportModalBtn" style="background:#3730A3;color:#ffffff;display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-weight:600;font-size:14px;border:none;cursor:pointer;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                Contact Support
              </button>
              <button type="button" class="btn btn-secondary" id="openLiveChatBtn" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-weight:600;font-size:14px;border:1px solid var(--border);background:var(--background);color:var(--text);cursor:pointer;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                Live Chat
              </button>
            </div>
          </div>

        </div>

        <!-- Meta Footer Strip Inside Card -->
        <div style="margin-top:24px;padding-top:18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;font-size:13px;color:var(--text-muted);">
          <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;gap:6px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              +91 (02697) 265011 / +1 (234) 567-890
            </span>
            <span style="display:inline-flex;align-items:center;gap:6px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              support@studenthub.edu
            </span>
          </div>
          <div style="display:flex;gap:16px;">
            <a href="../pages/about.php" style="color:var(--text-muted);text-decoration:none;">Privacy Policy</a>
            <span>•</span>
            <a href="../pages/faq.php" style="color:var(--text-muted);text-decoration:none;">Terms of Service</a>
          </div>
        </div>
      </section>

      <!-- SUPPORT MODAL (Submit a Help Ticket) -->
      <div id="supportModal" class="modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:9999;place-items:center;padding:20px;">
        <div class="card" style="background:var(--surface);width:min(100%, 580px);border-radius:18px;border:1px solid var(--border);box-shadow:0 20px 40px rgba(0,0,0,0.25);overflow:hidden;animation:modalFadeIn 0.25s ease;">
          <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:36px;height:36px;border-radius:10px;background:#EEF2FF;display:grid;place-items:center;color:#3730A3;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              </div>
              <div>
                <h3 style="margin:0;font-size:17px;font-weight:700;">Submit a Support Ticket</h3>
                <small style="color:var(--text-muted);">Direct to Academic & Technical Support</small>
              </div>
            </div>
            <button type="button" id="closeSupportModalBtn" style="background:none;border:none;font-size:22px;color:var(--text-muted);cursor:pointer;line-height:1;">✕</button>
          </div>

          <form method="post" action="student_help.php" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <input type="hidden" name="action" value="submit_help_ticket">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              <div class="form-group">
                <label style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Student Name</label>
                <input type="text" class="input" value="<?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:var(--background);cursor:not-allowed;">
              </div>
              <div class="form-group">
                <label style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Student ID</label>
                <input type="text" class="input" value="<?= htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:var(--background);cursor:not-allowed;">
              </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              <div class="form-group">
                <label for="ticketCategory" style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Help Topic / Category</label>
                <select id="ticketCategory" name="category" class="input" required>
                  <option value="Login & Access">Login & Access</option>
                  <option value="Courses & Registration">Courses & Registration</option>
                  <option value="Attendance & Schedule">Attendance & Schedule</option>
                  <option value="Assignments & Results">Assignments & Results</option>
                  <option value="Account Settings">Account Settings</option>
                  <option value="General Inquiry" selected>General Inquiry</option>
                </select>
              </div>
              <div class="form-group">
                <label for="ticketPriority" style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Priority Level</label>
                <select id="ticketPriority" name="priority" class="input">
                  <option value="Normal" selected>Normal (Standard turnaround)</option>
                  <option value="High">High (Impacting coursework)</option>
                  <option value="Urgent">Urgent (Immediate deadline)</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="ticketSubject" style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Subject / Issue Summary</label>
              <input type="text" id="ticketSubject" name="subject" class="input" placeholder="e.g. Need assistance with lab batch reschedule" required>
            </div>

            <div class="form-group">
              <label for="ticketMessage" style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Detailed Explanation</label>
              <textarea id="ticketMessage" name="message" class="input" rows="4" placeholder="Describe what occurred, any error messages, or details to help us investigate..." required></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:8px;">
              <button type="button" class="btn btn-secondary" id="cancelSupportModalBtn">Cancel</button>
              <button type="submit" class="btn btn-primary" style="background:#3730A3;border:none;">Submit Ticket</button>
            </div>
          </form>
        </div>
      </div>

      <!-- TOPIC DETAILS MODAL (Instant Help Guidelines) -->
      <div id="topicDetailModal" class="modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:9999;place-items:center;padding:20px;">
        <div class="card" style="background:var(--surface);width:min(100%, 620px);border-radius:18px;border:1px solid var(--border);box-shadow:0 20px 40px rgba(0,0,0,0.25);overflow:hidden;">
          <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div id="modalTopicIcon" style="width:36px;height:36px;border-radius:10px;display:grid;place-items:center;"></div>
              <h3 id="modalTopicTitle" style="margin:0;font-size:18px;font-weight:700;"></h3>
            </div>
            <button type="button" onclick="closeTopicModal()" style="background:none;border:none;font-size:22px;color:var(--text-muted);cursor:pointer;line-height:1;">✕</button>
          </div>
          <div id="modalTopicBody" style="padding:24px;font-size:14px;line-height:1.65;color:var(--text);">
            <!-- Dynamic guidance rendered via JS -->
          </div>
          <div style="padding:16px 24px;background:var(--background);border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;color:var(--text-muted);">Did this resolve your query?</span>
            <div style="display:flex;gap:10px;">
              <button type="button" class="btn btn-secondary btn-sm" onclick="closeTopicModal()">Yes, thanks!</button>
              <button type="button" class="btn btn-primary btn-sm" onclick="openSupportFromTopic()">Need more help →</button>
            </div>
          </div>
        </div>
      </div>

      <!-- LIVE CHAT SLIDE-IN ASSISTANT -->
      <div id="liveChatDrawer" style="display:none;position:fixed;bottom:24px;right:24px;width:min(100% - 48px, 380px);height:500px;background:var(--surface);border:1px solid var(--border);border-radius:18px;box-shadow:0 20px 40px rgba(0,0,0,0.25);z-index:9999;flex-direction:column;overflow:hidden;">
        <!-- Chat Header -->
        <div style="padding:16px 20px;background:#3730A3;color:#ffffff;display:flex;align-items:center;justify-content:space-between;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.2);display:grid;place-items:center;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div>
              <strong style="display:block;font-size:14px;color:#ffffff;">StudentHub Assistant</strong>
              <small style="color:#c7d2fe;font-size:11px;">● Online · Immediate answers</small>
            </div>
          </div>
          <button type="button" id="closeLiveChatBtn" style="background:none;border:none;color:#ffffff;font-size:20px;cursor:pointer;line-height:1;">✕</button>
        </div>

        <!-- Chat Messages -->
        <div id="chatMessages" style="flex:1;padding:16px;overflow-y:auto;display:flex;flex-direction:column;gap:12px;background:var(--background);font-size:13px;">
          <div style="align-self:flex-start;background:var(--surface);border:1px solid var(--border);padding:12px 14px;border-radius:14px 14px 14px 2px;max-width:85%;line-height:1.5;">
            👋 Hi <?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?>! How can I assist you with your Semester 3 studies today? Pick a quick topic below or type your question:
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:6px;" id="chatPills">
            <button type="button" class="btn btn-secondary btn-sm" style="font-size:11px;padding:4px 10px;" onclick="sendQuickChat('Where is my timetable?')">📅 View Timetable</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size:11px;padding:4px 10px;" onclick="sendQuickChat('What is the attendance rule?')">📊 Attendance Rule</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size:11px;padding:4px 10px;" onclick="sendQuickChat('How do I submit assignments?')">📝 Assignments</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size:11px;padding:4px 10px;" onclick="sendQuickChat('Who is my faculty coordinator?')">👨‍🏫 Faculty List</button>
          </div>
        </div>

        <!-- Chat Input -->
        <form id="chatForm" onsubmit="handleChatSubmit(event)" style="padding:12px;background:var(--surface);border-top:1px solid var(--border);display:flex;gap:8px;">
          <input type="text" id="chatInput" placeholder="Type your question..." style="flex:1;border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;background:var(--background);color:var(--text);outline:none;">
          <button type="submit" style="background:#3730A3;color:#ffffff;border:none;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer;">Send</button>
        </form>
      </div>

    </main>

    <div class="mobile-scrim" id="mobileScrim"></div>
    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

  <script>
    // Topic guides data
    const topicGuides = {
      'Login & Access': {
        title: 'Login & Access Troubleshooting',
        color: '#4F46E5',
        bg: '#EEF2FF',
        content: `
          <h4 style="margin:0 0 8px;font-size:15px;color:var(--primary);">1. How to reset student password</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">Navigate to the Login screen and click on "Forgot password" or contact your CSPIT department admin to regenerate a secure temporary hash.</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:var(--primary);">2. Accepted Credentials</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">You can sign into StudentHub using either your registered <strong>Institutional Email</strong> (e.g. 25cs045@charusat.edu.in) or your <strong>Student ID</strong> (e.g. 25CS045).</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:var(--primary);">3. Session Inactivity Timeout</h4>
          <p style="margin:0;color:var(--text-muted);">For security across shared campus computer lab terminals, sessions automatically time out after <strong>15 minutes of inactivity</strong>.</p>
        `
      },
      'Courses & Registration': {
        title: 'Courses & Registration Guidelines',
        color: '#10B981',
        bg: '#ECFDF5',
        content: `
          <h4 style="margin:0 0 8px;font-size:15px;color:#10B981;">1. Enrolled Semester 3 CSE Courses</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">You are actively enrolled in CSUC201 (FDSA), ITUE204 (WDF), CEUE203 (OOP), ITUC201 (FCN), MSUD203 (MATHS), and HSUV201 (HS) totaling 22 academic credit hours.</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:#10B981;">2. Course Syllabi & Lecture Material</h4>
          <p style="margin:0;color:var(--text-muted);">Course handouts, lab instruction manuals, and recommended reference textbooks can be accessed anytime from the <a href="student_courses.php" style="color:var(--primary);font-weight:600;">My Courses</a> section.</p>
        `
      },
      'Attendance & Schedule': {
        title: 'Attendance & Timetable Rules',
        color: '#F59E0B',
        bg: '#FFFBEB',
        content: `
          <h4 style="margin:0 0 8px;font-size:15px;color:#F59E0B;">1. University 75% Attendance Requirement</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">As per CHARUSAT university regulations, a minimum cumulative attendance of <strong>75%</strong> across lectures and laboratory sessions is required to qualify for end-semester university examinations.</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:#F59E0B;">2. Weekly Lecture & Lab Agenda</h4>
          <p style="margin:0;color:var(--text-muted);">Check your daily classroom sessions (Room 506) and division batch laboratories (CSPIT 631–638) on the interactive <a href="student_schedule.php" style="color:var(--primary);font-weight:600;">Schedule</a> matrix.</p>
        `
      },
      'Assignments & Results': {
        title: 'Assignments & Grade Point Policies',
        color: '#7C3AED',
        bg: '#F5F3FF',
        content: `
          <h4 style="margin:0 0 8px;font-size:15px;color:#7C3AED;">1. Submitting Practical Deliverables</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">Deliverables must be submitted before the stated 11:59 PM deadline. Check current deadlines on the <a href="student_assignments.php" style="color:var(--primary);font-weight:600;">Assignments</a> tracker.</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:#7C3AED;">2. SGPA / CGPA Calculation</h4>
          <p style="margin:0;color:var(--text-muted);">Semester Grade Point Average (SGPA) is calculated using weighted credit points according to the 10-point scale: AA=10, AB=9, BB=8, BC=7, CC=6, CD=5, DD=4, FF=0.</p>
        `
      },
      'Account Settings': {
        title: 'Account Settings & Privacy',
        color: '#06B6D4',
        bg: '#ECFEFF',
        content: `
          <h4 style="margin:0 0 8px;font-size:15px;color:#06B6D4;">1. Profile Information</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">View your enrolled department, enrolled degree program, registered contact number, and academic advisor on <a href="student_profile.php" style="color:var(--primary);font-weight:600;">My Profile</a>.</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:#06B6D4;">2. Dark & Light Theme Preference</h4>
          <p style="margin:0;color:var(--text-muted);">You can toggle between light and high-contrast dark themes anytime using the sun/moon button in the topbar or pressing <kbd>⌘K</kbd>.</p>
        `
      },
      'FAQ Access': {
        title: 'Frequently Asked Questions',
        color: '#EA580C',
        bg: '#FFF7ED',
        content: `
          <h4 style="margin:0 0 8px;font-size:15px;color:#EA580C;">1. Where can I find exam timetables?</h4>
          <p style="margin:0 0 14px;color:var(--text-muted);">Official semester examination schedules are published through the <a href="student_notices.php" style="color:var(--primary);font-weight:600;">Notices</a> circular board.</p>
          <h4 style="margin:0 0 8px;font-size:15px;color:#EA580C;">2. How do I participate in campus hackathons?</h4>
          <p style="margin:0;color:var(--text-muted);">RSVP for workshops, hackathons, and symposiums on the <a href="student_events.php" style="color:var(--primary);font-weight:600;">Events</a> page with real-time seat reservation.</p>
        `
      }
    };

    function openTopicModal(catName) {
      const data = topicGuides[catName] || topicGuides['Login & Access'];
      document.getElementById('modalTopicTitle').textContent = data.title;
      document.getElementById('modalTopicIcon').style.background = data.bg;
      document.getElementById('modalTopicIcon').style.color = data.color;
      document.getElementById('modalTopicIcon').innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>`;
      document.getElementById('modalTopicBody').innerHTML = data.content;
      document.getElementById('topicDetailModal').style.display = 'grid';
    }

    function closeTopicModal() {
      document.getElementById('topicDetailModal').style.display = 'none';
    }

    function openSupportFromTopic() {
      closeTopicModal();
      openSupportModal();
    }

    // Support Modal controls
    const supportModal = document.getElementById('supportModal');
    const openSupportBtn = document.getElementById('openSupportModalBtn');
    const closeSupportBtn = document.getElementById('closeSupportModalBtn');
    const cancelSupportBtn = document.getElementById('cancelSupportModalBtn');

    function openSupportModal(preselectedCategory = '') {
      if (preselectedCategory) {
        const catSelect = document.getElementById('ticketCategory');
        if (catSelect) catSelect.value = preselectedCategory;
      }
      supportModal.style.display = 'grid';
    }

    function closeSupportModal() {
      supportModal.style.display = 'none';
    }

    if (openSupportBtn) openSupportBtn.addEventListener('click', () => openSupportModal());
    if (closeSupportBtn) closeSupportBtn.addEventListener('click', closeSupportModal);
    if (cancelSupportBtn) cancelSupportBtn.addEventListener('click', closeSupportModal);

    // Live Chat Assistant
    const liveChatDrawer = document.getElementById('liveChatDrawer');
    const openLiveChatBtn = document.getElementById('openLiveChatBtn');
    const closeLiveChatBtn = document.getElementById('closeLiveChatBtn');
    const chatMessages = document.getElementById('chatMessages');

    if (openLiveChatBtn) {
      openLiveChatBtn.addEventListener('click', () => {
        liveChatDrawer.style.display = 'flex';
      });
    }

    if (closeLiveChatBtn) {
      closeLiveChatBtn.addEventListener('click', () => {
        liveChatDrawer.style.display = 'none';
      });
    }

    function sendQuickChat(msg) {
      appendChatMessage(msg, 'user');
      setTimeout(() => {
        let reply = "I'm looking into that for you. For detailed individual queries, please submit a support ticket via 'Contact Support'!";
        const lower = msg.toLowerCase();
        if (lower.includes('timetable') || lower.includes('schedule')) {
          reply = "Your official Semester 3 timetable is live under 'Schedule'. Classes begin at 9:10 AM in Room 506. Labs are held in CSPIT 631–638.";
        } else if (lower.includes('attendance')) {
          reply = "CHARUSAT university policy requires a minimum of 75% attendance in both Theory and Practical sessions. You can monitor your per-course percentages on the 'Attendance' page.";
        } else if (lower.includes('assignment')) {
          reply = "You have 3 active assignments due this week (BST Problem set, SQL Normalization, and OS Process worksheet). Check 'Assignments' for submission rubrics!";
        } else if (lower.includes('faculty') || lower.includes('coordinator')) {
          reply = "Your Semester 3 Time Table Coordinator is Prof. Avani Khokhariya / Hemang A Thakar, and HOD is Dr. Amit Thakkar.";
        }
        appendChatMessage(reply, 'bot');
      }, 500);
    }

    function handleChatSubmit(e) {
      e.preventDefault();
      const input = document.getElementById('chatInput');
      const val = input.value.trim();
      if (!val) return;
      input.value = '';
      sendQuickChat(val);
    }

    function appendChatMessage(text, sender) {
      const div = document.createElement('div');
      if (sender === 'user') {
        div.style.cssText = 'align-self:flex-end;background:#3730A3;color:#ffffff;padding:10px 14px;border-radius:14px 14px 2px 14px;max-width:85%;line-height:1.5;';
      } else {
        div.style.cssText = 'align-self:flex-start;background:var(--surface);border:1px solid var(--border);color:var(--text);padding:10px 14px;border-radius:14px 14px 14px 2px;max-width:85%;line-height:1.5;';
      }
      div.textContent = text;
      chatMessages.appendChild(div);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Interactive Search on 6 Topics
    const issueSearchInput = document.getElementById('helpIssueSearch');
    const issueSearchBtn = document.getElementById('helpSearchBtn');
    const topicCards = document.querySelectorAll('.help-topic-card');

    function filterTopics() {
      const q = (issueSearchInput.value || '').trim().toLowerCase();
      topicCards.forEach(card => {
        const text = (card.getAttribute('data-keywords') || '') + ' ' + card.innerText.toLowerCase();
        if (q === '' || text.includes(q)) {
          card.style.display = 'flex';
          card.style.opacity = '1';
        } else {
          card.style.display = 'none';
        }
      });
    }

    if (issueSearchInput) issueSearchInput.addEventListener('input', filterTopics);
    if (issueSearchBtn) issueSearchBtn.addEventListener('click', filterTopics);

    // Close modals when clicking backdrop
    window.addEventListener('click', (e) => {
      if (e.target === supportModal) closeSupportModal();
      if (e.target === document.getElementById('topicDetailModal')) closeTopicModal();
    });
  </script>


