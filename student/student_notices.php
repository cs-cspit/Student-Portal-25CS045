<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'Campus notices';
$pageCrumb = 'Notices';
$activeRoute = 'notices';
$portalType = 'student';

$noticesFile = dirname(__DIR__) . '/data/notices.json';
$notices = [];
if (file_exists($noticesFile)) {
    $notices = json_decode((string)file_get_contents($noticesFile), true) ?: [];
}

function app_notice_badge_class(string $cat): string {
    return match(strtolower(trim($cat))) {
        'academic' => 'badge-warning',
        'library' => 'badge-info',
        'events' => 'badge-success',
        'department' => 'badge-neutral',
        'general' => 'badge-info',
        default => 'badge-neutral'
    };
}

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="app-shell">
  <?php require dirname(__DIR__) . '/includes/student_sidebar.php'; ?>

  <div class="workspace">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>Official campus notices</h1>
          <p>Important announcements, circulars, exam notifications, and institutional advisories.</p>
        </div>
        <div class="header-actions">
          <span class="badge badge-info"><?= count($notices) ?> Published <?= count($notices) === 1 ? 'notice' : 'notices' ?></span>
        </div>
      </div>

      <?php if (empty($notices)): ?>
        <div class="card" style="padding:48px 24px;text-align:center;">
          <div style="width:48px;height:48px;border-radius:50%;background:var(--border-subtle);display:inline-grid;place-items:center;margin-bottom:12px;color:var(--text-muted);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
          </div>
          <h2 style="font-size:16px;margin:0 0 6px;">No campus notices at this time</h2>
          <p style="color:var(--text-muted);font-size:13px;max-width:440px;margin:0 auto;">New announcements and official communications published by the university administration will appear here automatically.</p>
        </div>
      <?php else: ?>
        <div class="card-stack">
          <?php foreach ($notices as $n): 
            $cat = (string)($n['category'] ?? 'General');
            $badgeClass = app_notice_badge_class($cat);
            $rawDate = (string)($n['date'] ?? '');
            $ts = strtotime($rawDate);
            $displayDate = $ts !== false ? date('d M Y', $ts) : $rawDate;
            $noticeId = (int)($n['id'] ?? 0);
          ?>
            <article class="card" style="padding:24px;" data-search="<?= htmlspecialchars(($n['title'] ?? '') . ' ' . $cat . ' ' . ($n['author'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></span>
                <time class="tabular-nums" style="color:var(--text-muted);font-size:12px;"><?= htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8') ?></time>
              </div>
              <h2 style="margin:0 0 10px;font-size:17px;line-height:1.4;">
                <?= htmlspecialchars((string)($n['title'] ?? 'Notice'), ENT_QUOTES, 'UTF-8') ?>
              </h2>
              <p style="margin:0 0 16px;color:var(--text);font-size:14px;line-height:1.65;">
                <?= nl2br(htmlspecialchars((string)($n['content'] ?? ''), ENT_QUOTES, 'UTF-8')) ?>
              </p>
              <div style="padding-top:12px;border-top:1px solid var(--border-subtle);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                <small style="color:var(--text-muted);">Issued by: <strong><?= htmlspecialchars((string)($n['author'] ?? 'Administration'), ENT_QUOTES, 'UTF-8') ?></strong></small>
                <span style="font-size:12px;color:var(--text-light);font-variant-numeric:tabular-nums;">Reference: SH-NOT-<?= $noticeId > 0 ? str_pad((string)$noticeId, 4, '0', STR_PAD_LEFT) : '0001' ?></span>
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
