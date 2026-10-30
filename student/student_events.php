<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Student');
app_no_cache_headers();

$pageTitle = 'Campus events';
$pageCrumb = 'Events';
$activeRoute = 'events';
$portalType = 'student';

$student = $_SESSION['student'] ?? [];
$studentId = (string)($student['student_id'] ?? '');

$events = [];
$myRegistrations = [];
$dbError = null;

try {
    $db = app_mysqli();
    
    // Fetch active events with registration counts and check if student is registered
    $sql = "SELECT e.*, 
            COUNT(r.registration_id) AS registrations,
            MAX(CASE WHEN r.student_id = ? AND r.status = 'registered' THEN 1 ELSE 0 END) AS is_registered
            FROM events e
            LEFT JOIN registrations r ON r.event_id = e.event_id AND r.status = 'registered'
            WHERE e.status = 'open'
            GROUP BY e.event_id
            ORDER BY e.event_date ASC, e.start_time ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $studentId);
    $stmt->execute();
    $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch student's registrations
    $regSql = "SELECT r.registered_at, r.status, e.title, e.event_date, e.start_time, e.location, e.category, e.poster_path
               FROM registrations r
               JOIN events e ON e.event_id = r.event_id
               WHERE r.student_id = ? AND r.status = 'registered'
               ORDER BY e.event_date ASC";
    $regStmt = $db->prepare($regSql);
    $regStmt->bind_param('s', $studentId);
    $regStmt->execute();
    $myRegistrations = $regStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $regStmt->close();

} catch (Throwable $e) {
    $dbError = 'Live database unavailable. Showing local event previews.';
    $fallbackPath = dirname(__DIR__) . '/data/events-fallback.json';
    if (file_exists($fallbackPath)) {
        $events = json_decode((string)file_get_contents($fallbackPath), true) ?: [];
    }
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
          <h1>Events & opportunities</h1>
          <p>Discover technical workshops, research seminars, hackathons, and cultural programs.</p>
        </div>
        <div class="header-actions">
          <span class="badge badge-info"><?= count($events) ?> Upcoming events</span>
          <?php if (!empty($myRegistrations)): ?>
            <span class="badge badge-success"><?= count($myRegistrations) ?> Places reserved</span>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($dbError): ?>
        <div class="alert alert-warning">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
          <div><?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <div id="rsvpFeedback" class="alert alert-success" style="display:none;"></div>

      <!-- If student has registered events, show My Registrations banner -->
      <?php if (!empty($myRegistrations)): ?>
        <section class="card" style="margin-bottom:24px;">
          <div class="card-header">
            <div>
              <h2>Your reserved places</h2>
              <p>Events you are currently registered to attend</p>
            </div>
            <span class="badge badge-success"><?= count($myRegistrations) ?> Confirmed</span>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th scope="col">Event title</th>
                  <th scope="col">Category</th>
                  <th scope="col">Date & time</th>
                  <th scope="col">Location</th>
                  <th scope="col">RSVP status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($myRegistrations as $reg): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($reg['title'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($reg['category'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td class="tabular-nums"><?= htmlspecialchars($reg['event_date'] . ' at ' . $reg['start_time'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reg['location'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-success"><span class="badge-dot"></span> Confirmed</span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      <?php endif; ?>

      <!-- Available Events Grid -->
      <h2 style="font-size:18px;margin:28px 0 16px;">All upcoming campus events</h2>
      <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;">
        <?php foreach ($events as $ev): 
          $registered = (int)($ev['is_registered'] ?? 0) === 1;
          $cap = isset($ev['capacity']) && $ev['capacity'] !== null ? (int)$ev['capacity'] : null;
          $regs = (int)($ev['registrations'] ?? 0);
          $seatsLeft = $cap === null ? null : max(0, $cap - $regs);
          $isSoldOut = $seatsLeft === 0;
        ?>
          <article class="card" style="display:flex;flex-direction:column;justify-content:space-between;padding:22px;" data-search="<?= htmlspecialchars($ev['title'] . ' ' . $ev['category'] . ' ' . $ev['location'], ENT_QUOTES, 'UTF-8') ?>">
            <div>
              <?php if (!empty($ev['poster_path']) && file_exists(__DIR__ . '/' . $ev['poster_path'])): ?>
                <div style="margin:-22px -22px 16px -22px;height:160px;overflow:hidden;border-top-left-radius:inherit;border-top-right-radius:inherit;">
                  <img src="<?= htmlspecialchars($ev['poster_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($ev['title'], ENT_QUOTES, 'UTF-8') ?>" style="width:100%;height:100%;object-fit:cover;">
                </div>
              <?php endif; ?>

              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <span class="badge badge-info"><?= htmlspecialchars($ev['category'] ?? 'Campus', ENT_QUOTES, 'UTF-8') ?></span>
                <span class="badge badge-neutral tabular-nums"><?= htmlspecialchars($ev['event_date'], ENT_QUOTES, 'UTF-8') ?></span>
              </div>

              <h3 style="margin:0 0 8px;font-size:17px;line-height:1.35;">
                <?= htmlspecialchars($ev['title'], ENT_QUOTES, 'UTF-8') ?>
              </h3>

              <p style="margin:0 0 14px;color:var(--text-muted);font-size:13px;line-height:1.6;">
                <?= htmlspecialchars($ev['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
              </p>

              <div style="font-size:12px;color:var(--text-muted);display:flex;flex-direction:column;gap:5px;margin-bottom:18px;">
                <div>📍 Venue: <strong><?= htmlspecialchars($ev['location'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                <div>⏰ Time: <strong><?= htmlspecialchars($ev['start_time'] ?? 'TBA', ENT_QUOTES, 'UTF-8') ?></strong></div>
                <div>👥 Capacity: <strong><?= $seatsLeft === null ? 'Open registration' : $seatsLeft . ' seats remaining' ?></strong></div>
              </div>
            </div>

            <div>
              <button type="button" class="btn <?= $registered ? 'btn-secondary' : 'btn-primary' ?> btn-sm" style="width:100%;" data-rsvp-id="<?= (int)$ev['event_id'] ?>" <?= ($registered || $isSoldOut) ? 'disabled' : '' ?>>
                <?= $registered ? '✓ You are registered' : ($isSoldOut ? 'Event fully booked' : 'Reserve a place →') ?>
              </button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/includes/bottom_nav.php'; ?>
    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

  <script>
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-rsvp-id]');
      if (!btn) return;
      btn.disabled = true;
      btn.textContent = 'Saving place…';
      const eventId = btn.getAttribute('data-rsvp-id');
      const feedback = document.getElementById('rsvpFeedback');

      try {
        const body = new URLSearchParams({
          event_id: eventId,
          csrf_token: <?= json_encode(app_csrf_token()) ?>
        });
        const res = await fetch('../api/events_api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
          credentials: 'same-origin',
          body
        });
        const data = await res.json();
        if (data.ok) {
          btn.className = 'btn btn-secondary btn-sm';
          btn.textContent = '✓ You are registered';
          if (feedback) {
            feedback.style.display = 'flex';
            feedback.textContent = data.message || 'Your registration has been confirmed!';
          }
        } else {
          alert(data.message || 'Registration failed.');
          btn.disabled = false;
          btn.textContent = 'Reserve a place →';
        }
      } catch (err) {
        alert('Network request failed. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Reserve a place →';
      }
    });
  </script>

