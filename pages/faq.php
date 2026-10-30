<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
app_no_cache_headers();

$faqsFile = dirname(__DIR__) . '/data/faqs.json';
$faqs = file_exists($faqsFile) ? (json_decode((string)file_get_contents($faqsFile), true) ?: []) : [];

$categories = array_values(array_unique(array_column($faqs, 'category')));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#15163A">
  <title>Frequently Asked Questions · StudentHub</title>
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
          <small>HELP & FAQ</small>
        </div>
      </a>
      <div class="charusat-topbar-brand" style="margin-left:14px;" title="Charotar University of Science and Technology (CHARUSAT)">
        <img src="../images/charusat-logo.png" alt="CHARUSAT University Logo" class="charusat-logo-img">
      </div>
    </div>
    <nav style="display:flex;align-items:center;gap:18px;">
      <a class="landing-nav-link" href="../index.php">Home</a>
      <a class="landing-nav-link" href="about.php">About</a>
      <a class="landing-nav-link" href="contact.php">Contact</a>
      <a class="btn btn-primary" href="../login.php">Sign in</a>
    </nav>
  </header>

  <main style="width:min(100% - 48px, 900px);margin:0 auto;padding:48px 0 64px;">
    <div style="text-align:center;max-width:640px;margin:0 auto 36px;">
      <p class="auth-eyebrow">Knowledge Base & Assistance</p>
      <h1 style="font-size:34px;margin:8px 0 14px;">Frequently asked questions</h1>
      <p style="font-size:15px;color:var(--text-muted);line-height:1.6;">
        Find answers regarding student accounts, courses, attendance thresholds, and university services.
      </p>

      <div style="margin-top:20px;">
        <input type="search" id="faqSearch" class="form-control" placeholder="Search questions or keywords..." style="height:46px;padding:0 18px;font-size:15px;">
      </div>
    </div>

    <!-- Category Pills -->
    <div style="display:flex;justify-content:center;gap:8px;margin-bottom:32px;flex-wrap:wrap;">
      <button class="btn btn-primary btn-sm" data-category="all">All topics</button>
      <?php foreach ($categories as $cat): ?>
        <button class="btn btn-secondary btn-sm" data-category="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- FAQ Accordions -->
    <div id="faqList" style="display:flex;flex-direction:column;gap:14px;">
      <?php foreach ($faqs as $f): ?>
        <details class="card" style="padding:18px 22px;cursor:pointer;" data-cat="<?= htmlspecialchars($f['category'], ENT_QUOTES, 'UTF-8') ?>" data-search="<?= htmlspecialchars($f['question'] . ' ' . $f['answer'] . ' ' . $f['category'], ENT_QUOTES, 'UTF-8') ?>">
          <summary style="font-weight:600;font-size:15px;color:var(--text);display:flex;align-items:center;justify-content:space-between;user-select:none;">
            <span><?= htmlspecialchars($f['question'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="badge badge-info" style="font-size:11px;"><?= htmlspecialchars($f['category'], ENT_QUOTES, 'UTF-8') ?></span>
          </summary>
          <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border-subtle);color:var(--text-muted);font-size:14px;line-height:1.65;">
            <?= htmlspecialchars($f['answer'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  </main>

  <footer class="landing-footer">
    <div><strong>StudentHub</strong> · University Portal</div>
    <div style="display:flex;gap:16px;">
      <a href="../index.php">Home</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
    </div>
  </footer>

  <script>
    const searchInput = document.getElementById('faqSearch');
    const items = document.querySelectorAll('#faqList details');
    const catBtns = document.querySelectorAll('[data-category]');
    let currentCat = 'all';

    function filterFaqs() {
      const q = searchInput.value.trim().toLowerCase();
      items.forEach(item => {
        const text = (item.getAttribute('data-search') || '').toLowerCase();
        const cat = item.getAttribute('data-cat') || '';
        const matchText = q === '' || text.includes(q);
        const matchCat = currentCat === 'all' || cat === currentCat;
        item.style.display = (matchText && matchCat) ? '' : 'none';
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', filterFaqs);
    }

    catBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        catBtns.forEach(b => {
          b.className = 'btn btn-secondary btn-sm';
        });
        btn.className = 'btn btn-primary btn-sm';
        currentCat = btn.getAttribute('data-category');
        filterFaqs();
      });
    });
  </script>
</body>
</html>


