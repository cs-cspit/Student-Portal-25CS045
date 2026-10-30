<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

app_require_role('Admin');
app_no_cache_headers();

$pageTitle = 'Event management';
$pageCrumb = 'Events';
$activeRoute = 'events';
$portalType = 'admin';

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<style>
.event-form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}
.event-form-grid .full-span {
  grid-column: 1 / -1;
}
.event-row {
  display: grid;
  grid-template-columns: 100px 1fr auto;
  gap: 18px;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-subtle);
  background: var(--surface);
  transition: background 0.15s ease;
}
.event-row:hover {
  background: var(--surface-hover);
}
.poster-preview {
  width: 90px;
  height: 64px;
  object-fit: cover;
  border-radius: var(--radius-control);
  background: var(--border-subtle);
}
.event-actions {
  display: flex;
  gap: 8px;
}
.event-action {
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  border-radius: var(--radius-control);
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text);
  cursor: pointer;
  transition: all 0.15s ease;
}
.event-action:hover {
  background: var(--surface-hover);
  border-color: var(--text-muted);
}
.event-notice {
  padding: 12px 16px;
  border-radius: var(--radius-control);
  font-size: 13px;
  margin-bottom: 20px;
  background: var(--success-subtle);
  color: var(--success);
  border: 1px solid rgba(21, 128, 61, 0.25);
}
.event-notice.error {
  background: var(--error-subtle);
  color: var(--error);
  border-color: rgba(198, 40, 40, 0.25);
}
@media (max-width: 768px) {
  .event-form-grid {
    grid-template-columns: 1fr;
  }
  .event-row {
    grid-template-columns: 70px 1fr;
  }
  .event-row .event-actions {
    grid-column: 1 / -1;
  }
}
</style>

<div class="admin-shell">
  <?php require dirname(__DIR__) . '/includes/admin_sidebar.php'; ?>

  <div class="admin-main-wrap">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="page-content" id="pageContent" tabindex="-1">
      <div class="page-header">
        <div class="page-title-wrap">
          <h1>Campus event management</h1>
          <p>Create and edit campus programs, upload event posters, and manage registrations.</p>
        </div>
        <div class="header-actions">
          <button id="reload-events" class="btn btn-secondary btn-sm" type="button">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            Refresh event list
          </button>
        </div>
      </div>

      <p id="event-notice" class="event-notice" role="status" aria-live="polite" hidden></p>

      <!-- Event Create / Edit Form Card -->
      <section class="card" style="margin-bottom:28px;">
        <div class="card-header">
          <h2 id="form-title">Create an event</h2>
          <span class="badge badge-info">Fetch API Enabled</span>
        </div>
        <div class="card-body">
          <form id="event-form" class="event-form-grid" enctype="multipart/form-data">
            <input type="hidden" id="event-id" name="event_id">

            <div>
              <label for="title" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Event title</label>
              <input id="title" name="title" class="form-control" maxlength="140" placeholder="e.g. Annual Tech Summit" required>
            </div>

            <div>
              <label for="category" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Category</label>
              <input id="category" name="category" class="form-control" maxlength="60" placeholder="Workshop, Hackathon, Seminar..." required>
            </div>

            <div>
              <label for="event-date" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Event date</label>
              <input id="event-date" name="event_date" type="date" class="form-control" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
              <div>
                <label for="start-time" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Start time</label>
                <input id="start-time" name="start_time" type="time" class="form-control" required>
              </div>
              <div>
                <label for="end-time" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">End time (optional)</label>
                <input id="end-time" name="end_time" type="time" class="form-control">
              </div>
            </div>

            <div>
              <label for="location" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Location / Venue</label>
              <input id="location" name="location" class="form-control" maxlength="150" placeholder="Auditorium, Lab 2, Virtual..." required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:10px;">
              <div>
                <label for="capacity" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Capacity</label>
                <input id="capacity" name="capacity" type="number" min="1" max="65535" class="form-control" placeholder="Seats">
              </div>
              <div>
                <label for="poster" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Poster (JPG, PNG, WEBP &lt; 2MB)</label>
                <input id="poster" name="poster" type="file" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding:6px;">
              </div>
            </div>

            <div class="full-span">
              <label for="description" style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Event description</label>
              <textarea id="description" name="description" class="form-control" style="min-height:90px;" placeholder="Provide details about the session syllabus, speaker profile, and prerequisites..." required></textarea>
            </div>

            <div class="full-span" style="display:flex;gap:10px;justify-content:flex-end;">
              <button id="cancel-edit" class="btn btn-secondary" type="button" hidden>Cancel edit</button>
              <button id="save-event" class="btn btn-primary" type="submit">Create event</button>
            </div>
          </form>
        </div>
      </section>

      <!-- Event List Card -->
      <section class="card">
        <div class="card-header">
          <div>
            <h2>Campus event directory</h2>
            <p id="event-loading" style="margin:0;font-size:12px;color:var(--text-muted);" hidden>Loading event records...</p>
          </div>
        </div>
        <div id="event-list"></div>
      </section>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

  <script src="practicals/practical13/script.js" defer></script>

