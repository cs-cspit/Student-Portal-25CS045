<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/audit.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $allRequests = app_get_help_requests();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="StudentHub_Help_Inquiries_' . date('Ymd_His') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Ticket ID', 'Name', 'Student ID', 'Email', 'Phone', 'Category', 'Subject', 'Message', 'Status', 'Priority', 'Source', 'Admin Notes', 'Submitted At']);
    foreach ($allRequests as $row) {
        fputcsv($output, [
            $row['id'] ?? '',
            $row['name'] ?? '',
            $row['student_id'] ?? '',
            $row['email'] ?? '',
            $row['phone'] ?? '',
            $row['category'] ?? '',
            $row['subject'] ?? '',
            $row['message'] ?? '',
            $row['status'] ?? '',
            $row['priority'] ?? '',
            $row['source'] ?? '',
            $row['admin_notes'] ?? '',
            $row['created_at'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}

$notice = '';
$error = '';

// Handle POST actions: Status updates or Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app_csrf_is_valid()) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $action = trim((string)($_POST['action'] ?? ''));
        $ticketId = trim((string)($_POST['ticket_id'] ?? ''));

        if ($action === 'update_status' && $ticketId !== '') {
            $newStatus = trim((string)($_POST['status'] ?? 'open'));
            $adminNotes = trim((string)($_POST['admin_notes'] ?? ''));
            if (app_update_help_request($ticketId, $newStatus, $adminNotes)) {
                app_audit_log('help.update', "Updated ticket {$ticketId} to {$newStatus}");
                $notice = "Inquiry {$ticketId} status updated to " . strtoupper($newStatus) . ".";
            } else {
                $error = "Could not find ticket {$ticketId}.";
            }
        } elseif ($action === 'delete_ticket' && $ticketId !== '') {
            if (app_delete_help_request($ticketId)) {
                app_audit_log('help.delete', "Deleted support ticket {$ticketId}");
                $notice = "Inquiry {$ticketId} was successfully removed.";
            } else {
                $error = "Could not remove ticket {$ticketId}.";
            }
        }
    }
}

$pageTitle = 'Support Inquiries & Contact Applications';
$pageCrumb = 'Help & Inquiries';
$activeRoute = 'inquiries';
$portalType = 'admin';

// Load and filter tickets
$allTickets = app_get_help_requests();

$totalCount = count($allTickets);
$openCount = count(array_filter($allTickets, static fn($t) => ($t['status'] ?? 'open') === 'open'));
$inProgressCount = count(array_filter($allTickets, static fn($t) => ($t['status'] ?? '') === 'in_progress'));
$resolvedCount = count(array_filter($allTickets, static fn($t) => ($t['status'] ?? '') === 'resolved'));

// Filter inputs
$filterStatus = trim((string)($_GET['status'] ?? 'all'));
$filterSource = trim((string)($_GET['source'] ?? 'all'));
$filterSearch = trim((string)($_GET['q'] ?? ''));

$filteredTickets = array_filter($allTickets, static function ($t) use ($filterStatus, $filterSource, $filterSearch) {
    if ($filterStatus !== 'all' && ($t['status'] ?? 'open') !== $filterStatus) {
        return false;
    }
    if ($filterSource !== 'all' && ($t['source'] ?? '') !== $filterSource) {
        return false;
    }
    if ($filterSearch !== '') {
        $needle = strtolower($filterSearch);
        $haystack = strtolower(
            ($t['id'] ?? '') . ' ' .
            ($t['name'] ?? '') . ' ' .
            ($t['student_id'] ?? '') . ' ' .
            ($t['email'] ?? '') . ' ' .
            ($t['subject'] ?? '') . ' ' .
            ($t['category'] ?? '') . ' ' .
            ($t['message'] ?? '')
        );
        if (!str_contains($haystack, $needle)) {
            return false;
        }
    }
    return true;
});

require dirname(__DIR__) . '/includes/layout_head.php';
?>
<div class="admin-shell">
  <?php require dirname(__DIR__) . '/includes/admin_sidebar.php'; ?>

  <div class="admin-main-wrap">
    <?php require dirname(__DIR__) . '/includes/layout_topbar.php'; ?>

    <main class="admin-content" id="pageContent" tabindex="-1">
      
      <!-- Page Header -->
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
          <h1 style="font-size:26px;font-weight:700;margin:0 0 6px;">Support & Contact Inquiries</h1>
          <p style="font-size:14px;color:var(--text-muted);margin:0;">
            Review incoming student helpdesk requests, department inquiries, and public contact submissions.
          </p>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="admin_inquiries.php?export=csv" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export CSV
          </a>
        </div>
      </div>

      <?php if ($notice !== ''): ?>
        <div class="alert alert-success" style="margin-bottom:20px;padding:12px 18px;border-radius:10px;background:var(--success-subtle);color:var(--text);border-left:4px solid var(--success);">
          <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <div class="alert alert-error" style="margin-bottom:20px;padding:12px 18px;border-radius:10px;background:var(--error-subtle);color:var(--text);border-left:4px solid var(--error);">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <!-- 4 KPI Metrics Cards -->
      <section style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-bottom:24px;">
        <div class="card" style="padding:18px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px;">
          <span style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Total Received</span>
          <div style="font-size:28px;font-weight:700;margin-top:6px;color:var(--text);"><?= $totalCount ?></div>
          <small style="color:var(--text-muted);font-size:12px;">Portal + Contact form</small>
        </div>

        <div class="card" style="padding:18px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px;border-left:4px solid #EF4444;">
          <span style="font-size:12px;color:#EF4444;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Pending Action</span>
          <div style="font-size:28px;font-weight:700;margin-top:6px;color:#EF4444;"><?= $openCount ?></div>
          <small style="color:var(--text-muted);font-size:12px;">Awaiting staff review</small>
        </div>

        <div class="card" style="padding:18px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px;border-left:4px solid #F59E0B;">
          <span style="font-size:12px;color:#F59E0B;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">In Progress</span>
          <div style="font-size:28px;font-weight:700;margin-top:6px;color:#F59E0B;"><?= $inProgressCount ?></div>
          <small style="color:var(--text-muted);font-size:12px;">Assigned to department</small>
        </div>

        <div class="card" style="padding:18px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px;border-left:4px solid #10B981;">
          <span style="font-size:12px;color:#10B981;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Resolved</span>
          <div style="font-size:28px;font-weight:700;margin-top:6px;color:#10B981;"><?= $resolvedCount ?></div>
          <small style="color:var(--text-muted);font-size:12px;">Completed tickets</small>
        </div>
      </section>

      <!-- Filter Bar -->
      <section class="card" style="padding:16px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px;margin-bottom:24px;">
        <form method="get" action="admin_inquiries.php" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
          
          <div style="flex:1;min-width:240px;position:relative;">
            <input type="search" name="q" value="<?= htmlspecialchars($filterSearch, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search by name, ID, email, or keywords..." class="input" style="width:100%;height:38px;padding-left:14px;font-size:13px;">
          </div>

          <div style="min-width:140px;">
            <select name="status" class="input" style="height:38px;font-size:13px;" onchange="this.form.submit()">
              <option value="all" <?= $filterStatus === 'all' ? 'selected' : '' ?>>All Statuses</option>
              <option value="open" <?= $filterStatus === 'open' ? 'selected' : '' ?>>Pending / Open (<?= $openCount ?>)</option>
              <option value="in_progress" <?= $filterStatus === 'in_progress' ? 'selected' : '' ?>>In Progress (<?= $inProgressCount ?>)</option>
              <option value="resolved" <?= $filterStatus === 'resolved' ? 'selected' : '' ?>>Resolved (<?= $resolvedCount ?>)</option>
            </select>
          </div>

          <div style="min-width:140px;">
            <select name="source" class="input" style="height:38px;font-size:13px;" onchange="this.form.submit()">
              <option value="all" <?= $filterSource === 'all' ? 'selected' : '' ?>>All Channels</option>
              <option value="Student Portal" <?= $filterSource === 'Student Portal' ? 'selected' : '' ?>>Student Portal</option>
              <option value="Contact Form" <?= $filterSource === 'Contact Form' ? 'selected' : '' ?>>Contact Form</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary" style="height:38px;padding:0 18px;font-size:13px;">Filter</button>
          <?php if ($filterSearch !== '' || $filterStatus !== 'all' || $filterSource !== 'all'): ?>
            <a href="admin_inquiries.php" class="btn btn-secondary" style="height:38px;display:inline-grid;place-items:center;padding:0 14px;font-size:13px;">Clear</a>
          <?php endif; ?>
        </form>
      </section>

      <!-- Inquiries Table -->
      <section class="card" style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;">
        <div class="card-header" style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
          <h2 style="font-size:16px;font-weight:700;margin:0;">Inquiries Registry</h2>
          <span class="badge badge-neutral"><?= count($filteredTickets) ?> Records Listed</span>
        </div>

        <?php if (empty($filteredTickets)): ?>
          <div style="padding:48px 24px;text-align:center;color:var(--text-muted);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;opacity:0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            <p style="font-size:15px;margin:0 0 6px;">No support inquiries match the current filter criteria.</p>
            <small>Adjust search terms or clear filters to view all entries.</small>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="data-table" style="width:100%;font-size:13px;">
              <thead>
                <tr>
                  <th scope="col" style="width:120px;">Ticket / Date</th>
                  <th scope="col" style="width:180px;">Sender & Source</th>
                  <th scope="col" style="width:150px;">Topic / Category</th>
                  <th scope="col">Subject & Summary</th>
                  <th scope="col" style="width:120px;">Status</th>
                  <th scope="col" style="width:130px;text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($filteredTickets as $row): 
                  $st = strtolower((string)($row['status'] ?? 'open'));
                  $badgeClass = $st === 'resolved' ? 'badge-success' : ($st === 'in_progress' ? 'badge-warning' : 'badge-error');
                  $sourceClass = ($row['source'] ?? '') === 'Student Portal' ? 'badge-info' : 'badge-neutral';
                ?>
                  <tr>
                    <td>
                      <strong style="color:var(--primary);font-family:monospace;"><?= htmlspecialchars((string)($row['id'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></strong>
                      <small style="display:block;color:var(--text-muted);font-size:11px;margin-top:2px;">
                        <?= htmlspecialchars(date('d M Y, g:i A', strtotime((string)($row['created_at'] ?? 'now'))), ENT_QUOTES, 'UTF-8') ?>
                      </small>
                    </td>
                    <td>
                      <strong style="color:var(--text);display:block;"><?= htmlspecialchars((string)($row['name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></strong>
                      <small style="color:var(--text-muted);display:block;"><?= htmlspecialchars((string)($row['email'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></small>
                      <div style="margin-top:4px;display:flex;gap:4px;">
                        <span class="badge <?= $sourceClass ?>" style="font-size:10px;padding:2px 6px;"><?= htmlspecialchars((string)($row['source'] ?? 'Portal'), ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if (!empty($row['student_id'])): ?>
                          <span class="badge badge-neutral" style="font-size:10px;padding:2px 6px;"><?= htmlspecialchars((string)$row['student_id'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <span class="badge badge-info" style="font-size:11px;"><?= htmlspecialchars((string)($row['category'] ?? 'General'), ENT_QUOTES, 'UTF-8') ?></span>
                      <?php if (($row['priority'] ?? 'Normal') === 'Urgent'): ?>
                        <span class="badge badge-error" style="font-size:10px;padding:2px 6px;margin-top:4px;display:inline-block;">Urgent</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <strong style="display:block;color:var(--text);margin-bottom:3px;"><?= htmlspecialchars((string)($row['subject'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></strong>
                      <p style="margin:0;color:var(--text-muted);font-size:12px;line-height:1.4;max-width:380px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?= htmlspecialchars((string)($row['message'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                      </p>
                      <?php if (!empty($row['admin_notes'])): ?>
                        <div style="margin-top:4px;font-size:11px;color:#10B981;display:flex;align-items:center;gap:4px;">
                          <span>↳ Note:</span> <em><?= htmlspecialchars((string)$row['admin_notes'], ENT_QUOTES, 'UTF-8') ?></em>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge <?= $badgeClass ?>">
                        <span class="badge-dot"></span>
                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $st)), ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </td>
                    <td style="text-align:right;">
                      <div style="display:inline-flex;align-items:center;gap:6px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick='openReviewModal(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="View & update ticket">
                          Review
                        </button>
                        <form method="post" action="admin_inquiries.php" style="margin:0;" onsubmit="return confirm('Permanently remove this inquiry?')">
                          <input type="hidden" name="action" value="delete_ticket">
                          <input type="hidden" name="ticket_id" value="<?= htmlspecialchars((string)($row['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                          <button type="submit" class="btn-icon" style="color:var(--error);padding:6px;" title="Delete ticket">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <!-- REVIEW / RESOLUTION MODAL -->
      <div id="reviewModal" class="modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);z-index:9999;place-items:center;padding:20px;">
        <div class="card" style="background:var(--surface);width:min(100%, 600px);border-radius:16px;border:1px solid var(--border);box-shadow:0 20px 40px rgba(0,0,0,0.25);overflow:hidden;">
          <div style="padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
              <h3 id="modalTicketHeading" style="margin:0;font-size:17px;font-weight:700;">Ticket Details</h3>
              <small id="modalTicketSub" style="color:var(--text-muted);"></small>
            </div>
            <button type="button" onclick="closeReviewModal()" style="background:none;border:none;font-size:22px;color:var(--text-muted);cursor:pointer;line-height:1;">✕</button>
          </div>

          <form method="post" action="admin_inquiries.php" style="padding:22px;display:flex;flex-direction:column;gap:16px;">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="ticket_id" id="modalTicketIdInput" value="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

            <!-- Sender Info Summary -->
            <div style="background:var(--background);padding:14px 16px;border-radius:10px;border:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;">
              <div><strong>Sender:</strong> <span id="modalSenderName"></span></div>
              <div><strong>Contact:</strong> <span id="modalSenderEmail"></span></div>
              <div><strong>Category:</strong> <span id="modalCategory"></span></div>
              <div><strong>Source:</strong> <span id="modalSource"></span></div>
            </div>

            <!-- Subject & Full Message -->
            <div>
              <label style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">Inquiry Subject</label>
              <div id="modalSubject" style="font-weight:600;font-size:14px;color:var(--text);margin-bottom:12px;"></div>

              <label style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">Submitted Message</label>
              <div id="modalMessage" style="background:var(--background);padding:12px 14px;border-radius:10px;font-size:13px;line-height:1.6;border:1px solid var(--border);white-space:pre-wrap;max-height:160px;overflow-y:auto;"></div>
            </div>

            <!-- Status and Resolution Note -->
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;">
              <div class="form-group">
                <label for="modalStatusSelect" style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Update Status</label>
                <select id="modalStatusSelect" name="status" class="input" style="height:38px;font-size:13px;" required>
                  <option value="open">Pending / Open</option>
                  <option value="in_progress">In Progress</option>
                  <option value="resolved">Resolved</option>
                </select>
              </div>

              <div class="form-group">
                <label for="modalAdminNotes" style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">Administrator Resolution Note</label>
                <input type="text" id="modalAdminNotes" name="admin_notes" class="input" placeholder="e.g. Advised student on room 632 allocation" style="height:38px;font-size:13px;">
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;">
              <button type="button" class="btn btn-secondary" onclick="closeReviewModal()">Cancel</button>
              <button type="submit" class="btn btn-primary" style="background:#3730A3;border:none;">Save Status & Notes</button>
            </div>
          </form>
        </div>
      </div>

    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>

  <script>
    const reviewModal = document.getElementById('reviewModal');

    function openReviewModal(item) {
      document.getElementById('modalTicketIdInput').value = item.id || '';
      document.getElementById('modalTicketHeading').textContent = 'Ticket: ' + (item.id || '');
      document.getElementById('modalTicketSub').textContent = 'Submitted on ' + (item.created_at || '');
      document.getElementById('modalSenderName').textContent = (item.name || '') + (item.student_id ? ' (' + item.student_id + ')' : '');
      document.getElementById('modalSenderEmail').textContent = item.email || item.phone || '—';
      document.getElementById('modalCategory').textContent = item.category || '—';
      document.getElementById('modalSource').textContent = item.source || 'Portal';
      document.getElementById('modalSubject').textContent = item.subject || '—';
      document.getElementById('modalMessage').textContent = item.message || '—';
      document.getElementById('modalStatusSelect').value = item.status || 'open';
      document.getElementById('modalAdminNotes').value = item.admin_notes || '';
      reviewModal.style.display = 'grid';
    }

    function closeReviewModal() {
      reviewModal.style.display = 'none';
    }

    window.addEventListener('click', (e) => {
      if (e.target === reviewModal) closeReviewModal();
    });
  </script>

