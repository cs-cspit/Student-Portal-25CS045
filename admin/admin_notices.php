<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/audit.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$message = '';
$noticesFile = dirname(__DIR__) . '/data/notices.json';

// Ensure data/notices.json exists with seed notices if not yet created
if (!file_exists($noticesFile)) {
    $seed = [
        [
            'id' => 1,
            'title' => 'Mid-Semester Theory & Practical Assessment Timetable Published',
            'category' => 'Academic',
            'author' => 'Controller of Examinations',
            'date' => '2026-10-28',
            'content' => 'The complete timetable for Fall 2026 Mid-Semester examinations is now available for review.'
        ],
        [
            'id' => 2,
            'title' => 'Extended Reading Hall Hours for Assessment Period',
            'category' => 'Library',
            'author' => 'University Library Services',
            'date' => '2026-10-26',
            'content' => 'The central university library reading rooms will remain open until 10:00 PM on all weekdays.'
        ],
        [
            'id' => 3,
            'title' => 'Faculty Advisory Slots Open for Capstone Approvals',
            'category' => 'Department',
            'author' => 'Department of Computer Science',
            'date' => '2026-10-24',
            'content' => 'All Semester 4 and Semester 6 students preparing capstone proposals must meet with their faculty advisor.'
        ]
    ];
    file_put_contents($noticesFile, json_encode($seed, JSON_PRETTY_PRINT));
}

$notices = json_decode((string)file_get_contents($noticesFile), true) ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app_csrf_is_valid()) {
        $message = 'Session form expired. Please try again.';
    } else {
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'create') {
            $title = trim((string)($_POST['title'] ?? ''));
            $category = trim((string)($_POST['category'] ?? 'General'));
            $author = trim((string)($_POST['author'] ?? 'Administration'));
            $content = trim((string)($_POST['content'] ?? ''));

            if ($title !== '' && $content !== '') {
                $newNotice = [
                    'id' => time(),
                    'title' => $title,
                    'category' => $category,
                    'author' => $author,
                    'date' => date('Y-m-d'),
                    'content' => $content
                ];
                array_unshift($notices, $newNotice);
                file_put_contents($noticesFile, json_encode($notices, JSON_PRETTY_PRINT));
                app_audit_log('notice.create', "Published notice: {$title}");
                $message = 'Notice published successfully to the student portal.';
            }
        } elseif ($action === 'delete') {
            $delId = (int)($_POST['notice_id'] ?? 0);
            $notices = array_values(array_filter($notices, fn($n) => (int)$n['id'] !== $delId));
            file_put_contents($noticesFile, json_encode($notices, JSON_PRETTY_PRINT));
            app_audit_log('notice.delete', "Removed notice ID: {$delId}");
            $message = 'Notice removed.';
        }
    }
}

$pageTitle = 'Campus notices';
$pageCrumb = 'Notices';
$activeRoute = 'notices';
$portalType = 'admin';

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="admin-shell">
  <?php require dirname(__DIR__) . '/includes/admin_sidebar.php'; ?>

  <div class="admin-main-wrap">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>Notice board administration</h1>
          <p>Publish official campus announcements and communications displayed to students.</p>
        </div>
      </div>

      <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="two-column-layout">
        <!-- Publish Notice Form -->
        <section class="card">
          <div class="card-header">
            <h2>Publish new announcement</h2>
          </div>
          <div class="card-body">
            <form method="post" action="admin_notices.php">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="action" value="create">

              <div class="form-group">
                <label for="title">Notice heading</label>
                <input id="title" name="title" class="form-control" placeholder="e.g. Schedule for Mid-Term Lab Exams" required>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;" class="form-group">
                <div>
                  <label for="category">Category</label>
                  <select id="category" name="category" class="form-select">
                    <option value="Academic">Academic Office</option>
                    <option value="Library">Library Services</option>
                    <option value="Department">Department</option>
                    <option value="Events">Campus Events</option>
                    <option value="General">General Circular</option>
                  </select>
                </div>
                <div>
                  <label for="author">Issuing authority</label>
                  <input id="author" name="author" class="form-control" value="Office of the Dean" required>
                </div>
              </div>

              <div class="form-group">
                <label for="content">Notice text & instructions</label>
                <textarea id="content" name="content" class="form-control" style="min-height:110px;" placeholder="Provide complete circular details, affected batches, and deadlines..." required></textarea>
              </div>

              <button type="submit" class="btn btn-primary">Publish to student portal</button>
            </form>
          </div>
        </section>

        <!-- Current Published Notices -->
        <section class="card">
          <div class="card-header">
            <div>
              <h2>Published notices (<?= count($notices) ?>)</h2>
              <p>Active bulletins currently visible to students</p>
            </div>
          </div>
          <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
            <?php foreach ($notices as $n): ?>
              <article style="padding:16px;border:1px solid var(--border-subtle);border-radius:var(--radius-control);background:var(--background);" data-search="<?= htmlspecialchars($n['title'] . ' ' . $n['category'], ENT_QUOTES, 'UTF-8') ?>">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                  <span class="badge badge-info"><?= htmlspecialchars($n['category'], ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="tabular-nums" style="color:var(--text-muted);font-size:12px;"><?= htmlspecialchars($n['date'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <h3 style="margin:0 0 6px;font-size:15px;line-height:1.4;"><?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p style="margin:0 0 10px;font-size:13px;color:var(--text-muted);line-height:1.5;"><?= htmlspecialchars($n['content'], ENT_QUOTES, 'UTF-8') ?></p>
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px solid var(--border-subtle);">
                  <small style="color:var(--text-light);">Author: <?= htmlspecialchars($n['author'], ENT_QUOTES, 'UTF-8') ?></small>
                  <form method="post" action="admin_notices.php" style="margin:0;" onsubmit="return confirm('Remove this notice?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="notice_id" value="<?= (int)$n['id'] ?>">
                    <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--error);border-color:var(--error);padding:3px 8px;font-size:11px;">Delete</button>
                  </form>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

