<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
app_no_cache_headers();

$errors = [];
$success = '';

$name = '';
$email = '';
$mobile = '';
$course = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $course = trim((string)($_POST['course'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));

    if ($name === '') {
        $errors[] = 'Full name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $errors[] = 'Enter a valid 10-digit mobile number.';
    }
    if (!in_array($course, ['B.Tech', 'BCA', 'B.Sc', 'MCA'], true)) {
        $errors[] = 'Please select your department / program.';
    }
    if ($message === '') {
        $errors[] = 'Message content cannot be blank.';
    }

    if (empty($errors)) {
        $ticketId = app_save_help_request([
            'name' => $name,
            'email' => $email,
            'phone' => $mobile,
            'category' => $course !== '' ? $course . ' Department' : 'General Inquiry',
            'subject' => 'Public Contact Inquiry',
            'message' => $message,
            'source' => 'Contact Form',
            'priority' => 'Normal'
        ]);
        $success = 'Thank you for contacting StudentHub. Your message has been received by academic support (Reference: ' . $ticketId . ').';
        $name = $email = $mobile = $course = $message = '';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#15163A">
  <title>Contact & Academic Support · StudentHub</title>
  <link rel="stylesheet" href="../css/tokens.css">
  <link rel="stylesheet" href="../css/auth.css">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('studenthub_theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = savedTheme ? savedTheme : (prefersDark ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
      if (theme === 'dark') document.documentElement.classList.add('dark');
    })();
  </script>
</head>
<body class="landing-page">
  <header class="landing-header">
    <div style="display:flex;align-items:center;gap:14px;">
      <a class="auth-brand" href="../index.php">
        <span class="brand-mark">S</span>
        <div>
          <span>StudentHub</span>
          <small>SUPPORT DESK</small>
        </div>
      </a>
      <div class="charusat-topbar-brand" style="margin-left:14px;" title="Charotar University of Science and Technology (CHARUSAT)">
        <img src="../images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
      </div>
    </div>
    <nav style="display:flex;align-items:center;gap:18px;">
      <a class="landing-nav-link" href="../index.php">Home</a>
      <a class="landing-nav-link" href="about.php">About</a>
      <a class="landing-nav-link" href="faq.php">FAQs</a>
      <a class="btn btn-primary" href="../login.php">Sign in</a>
    </nav>
  </header>

  <main style="width:min(100% - 48px, 960px);margin:0 auto;padding:48px 0 64px;">
    <div style="max-width:600px;margin-bottom:32px;">
      <p class="auth-eyebrow">Assistance & Inquiries</p>
      <h1 style="font-size:34px;margin:8px 0 12px;">Contact academic support</h1>
      <p style="font-size:15px;color:var(--text-muted);line-height:1.6;">
        Have questions regarding enrolled courses, attendance queries, or technical access? Submit an inquiry directly to the student services desk.
      </p>
    </div>

    <?php if ($success !== ''): ?>
      <div class="form-alert form-alert-success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="form-alert form-alert-error" role="alert">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="grid" style="grid-template-columns:1.2fr 0.8fr;gap:36px;">
      <section class="auth-card">
        <form method="post" action="contact.php">
          <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" placeholder="Your legal name" required>
          </div>

          <div class="form-grid">
            <div class="field">
              <label for="email">Email address</label>
              <input id="email" name="email" type="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" placeholder="student@example.edu" required>
            </div>
            <div class="field">
              <label for="mobile">Mobile number (10 digits)</label>
              <input id="mobile" name="mobile" type="tel" maxlength="10" value="<?= htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8') ?>" placeholder="9876543210" required>
            </div>
          </div>

          <div class="field">
            <label for="course">Program / Department</label>
            <select id="course" name="course" required>
              <option value="">Select your program</option>
              <option value="B.Tech" <?= $course === 'B.Tech' ? 'selected' : '' ?>>B.Tech Computer Science</option>
              <option value="BCA" <?= $course === 'BCA' ? 'selected' : '' ?>>BCA</option>
              <option value="B.Sc" <?= $course === 'B.Sc' ? 'selected' : '' ?>>B.Sc</option>
              <option value="MCA" <?= $course === 'MCA' ? 'selected' : '' ?>>MCA</option>
            </select>
          </div>

          <div class="field">
            <label for="message">Message / Inquiry</label>
            <textarea id="message" name="message" class="form-control" style="min-height:120px;" placeholder="Describe your question or technical assistance request..." required><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <button type="submit" class="auth-submit">
            <span>Send message to support</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
          </button>
        </form>
      </section>

      <section style="display:flex;flex-direction:column;gap:20px;">
        <article class="auth-card" style="padding:24px;">
          <h2 style="font-size:16px;margin:0 0 10px;">Academic Affairs Office</h2>
          <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0 0 12px;">
            Main Campus, Building A · Room 102<br>
            Office Hours: Mon–Fri, 09:00 AM – 05:00 PM
          </p>
          <div style="font-size:13px;color:var(--text);">
            📧 support@studenthub.local<br>
            📞 +91 79 2345 6789
          </div>
        </article>

        <article class="auth-card" style="padding:24px;">
          <h2 style="font-size:16px;margin:0 0 10px;">Data Record Alignment</h2>
          <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0;">
            Submissions via this form are recorded in accordance with Practical 7 form validation standards and appended to <code>data/registrations.csv</code>.
          </p>
        </article>
      </section>
    </div>
  </main>

  <footer class="landing-footer">
    <div><strong>StudentHub</strong> · University Portal</div>
    <div style="display:flex;gap:16px;">
      <a href="../index.php">Home</a>
      <a href="about.php">About</a>
      <a href="faq.php">FAQs</a>
    </div>
  </footer>
</body>
</html>


