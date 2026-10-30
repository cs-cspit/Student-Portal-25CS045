<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/audit.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/db.php';

app_require_role('Admin');
app_no_cache_headers();

$db = app_mysqli();
$courses = ['B.Tech', 'BCA', 'B.Sc Computer Science', 'MCA'];
$message = (string)($_SESSION['manage_notice'] ?? '');
$error = '';
unset($_SESSION['manage_notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app_csrf_is_valid()) {
        $error = 'Form expired. Refresh and try again.';
    } else {
        $action = (string)($_POST['action'] ?? '');
        $id = trim((string)($_POST['student_id'] ?? ''));

        if ($action === 'delete') {
            $stmt = $db->prepare("UPDATE students SET status='inactive' WHERE student_id=? AND role='Student'");
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $message = $stmt->affected_rows ? 'Student marked inactive successfully.' : 'No student record was changed.';
            if ($stmt->affected_rows) {
                app_audit_log('student.soft_delete', 'Student ID: ' . $id);
            }
            $stmt->close();
        } elseif (in_array($action, ['add', 'update'], true)) {
            $name = trim((string)($_POST['full_name'] ?? ''));
            $age = filter_var($_POST['age'] ?? null, FILTER_VALIDATE_INT);
            $email = strtolower(trim((string)($_POST['email'] ?? '')));
            $phone = trim((string)($_POST['phone'] ?? ''));
            $gender = trim((string)($_POST['gender'] ?? ''));
            $course = trim((string)($_POST['course'] ?? ''));

            if ($id === '' || $name === '' || !$age || $age < 15 || $age > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $gender === '' || !in_array($course, $courses, true)) {
                $error = 'Please check all student details and try again.';
            } elseif ($action === 'add') {
                $password = (string)($_POST['password'] ?? '');
                if (strlen($password) < 8) {
                    $error = 'Initial password must be at least 8 characters long.';
                } else {
                    try {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO students (student_id, full_name, age, email, phone, gender, course, password_hash, role, status) VALUES (?,?,?,?,?,?,?,?,'Student','active')");
                        $stmt->bind_param('ssisssss', $id, $name, $age, $email, $phone, $gender, $course, $hash);
                        $stmt->execute();
                        $stmt->close();
                        $message = 'Student account created successfully.';
                        app_audit_log('student.create', 'Student ID: ' . $id);
                    } catch (mysqli_sql_exception $e) {
                        $error = $e->getCode() === 1062 ? 'Student ID or email already exists.' : 'Student could not be added.';
                        error_log('StudentHub CRUD insert: ' . $e->getMessage());
                    }
                }
            } else {
                try {
                    $stmt = $db->prepare("UPDATE students SET full_name=?, age=?, email=?, phone=?, gender=?, course=? WHERE student_id=? AND role='Student' AND status='active'");
                    $stmt->bind_param('sisssss', $name, $age, $email, $phone, $gender, $course, $id);
                    $stmt->execute();
                    $stmt->close();
                    $message = 'Student details updated successfully.';
                    app_audit_log('student.update', 'Student ID: ' . $id);
                } catch (mysqli_sql_exception $e) {
                    $error = $e->getCode() === 1062 ? 'That email address is already in use.' : 'Student could not be updated.';
                    error_log('StudentHub CRUD update: ' . $e->getMessage());
                }
            }
        }
    }
    $_SESSION['manage_notice'] = $error !== '' ? $error : $message;
    header('Location: student_management.php', true, 303);
    exit;
}

$search = trim((string)($_GET['search'] ?? ''));
$courseFilter = trim((string)($_GET['course'] ?? ''));
$status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? (string)$_GET['status'] : 'active';
$where = "role='Student' AND status=? AND (student_id LIKE ? OR full_name LIKE ? OR email LIKE ?)";
$like = '%' . $search . '%';

if (isset($_GET['export'])) {
    $sql = "SELECT student_id, full_name, age, email, phone, gender, course, status FROM students WHERE $where" . (in_array($courseFilter, $courses, true) ? ' AND course=?' : '') . ' ORDER BY full_name';
    $stmt = $db->prepare($sql);
    if (in_array($courseFilter, $courses, true)) {
        $stmt->bind_param('sssss', $status, $like, $like, $like, $courseFilter);
    } else {
        $stmt->bind_param('ssss', $status, $like, $like, $like);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="studenthub-students.csv"');
    $out = fopen('php://output', 'w');
    $savedFile = fopen(dirname(__DIR__) . '/data/student_records.csv', 'w');
    $headers = ['Student ID', 'Name', 'Age', 'Email', 'Phone', 'Gender', 'Course', 'Status'];
    fputcsv($out, $headers);
    if ($savedFile !== false) fputcsv($savedFile, $headers);

    while ($row = $result->fetch_assoc()) {
        $values = array_values($row);
        fputcsv($out, $values);
        if ($savedFile !== false) fputcsv($savedFile, $values);
    }
    fclose($out);
    if ($savedFile !== false) fclose($savedFile);
    $stmt->close();
    exit;
}

$sql = "SELECT student_id, full_name, age, email, phone, gender, course, status FROM students WHERE $where" . (in_array($courseFilter, $courses, true) ? ' AND course=?' : '') . ' ORDER BY full_name';
$stmt = $db->prepare($sql);
if (in_array($courseFilter, $courses, true)) {
    $stmt->bind_param('sssss', $status, $like, $like, $like, $courseFilter);
} else {
    $stmt->bind_param('ssss', $status, $like, $like, $like);
}
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = 'Student records';
$pageCrumb = 'Students';
$activeRoute = 'students';
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
          <h1>Student records & management</h1>
          <p>Search, filter, enroll new students, edit profile details, and export datasets.</p>
        </div>
        <div class="header-actions">
          <a class="btn btn-secondary btn-sm" href="?<?= http_build_query(['export' => 1, 'search' => $search, 'course' => $courseFilter, 'status' => $status]) ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export to CSV
          </a>
        </div>
      </div>

      <?php if ($message !== ''): 
        $isErr = str_contains(strtolower($message), 'could not') || str_contains(strtolower($message), 'already') || str_contains(strtolower($message), 'check') || str_contains(strtolower($message), 'expired');
      ?>
        <div class="alert <?= $isErr ? 'alert-error' : 'alert-success' ?>" role="status">
          <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <!-- Filter Bar -->
      <section class="card" style="margin-bottom:24px;padding:18px 22px;">
        <form method="get" action="student_management.php" style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;">
          <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Search query</label>
            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search by name, student ID, or email">
          </div>

          <div style="width:200px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Course filter</label>
            <select name="course" class="form-select">
              <option value="">All courses</option>
              <?php foreach ($courses as $c): ?>
                <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>" <?= $courseFilter === $c ? 'selected' : '' ?>><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="width:140px;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Status</label>
            <select name="status" class="form-select">
              <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active records</option>
              <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive (soft-deleted)</option>
            </select>
          </div>

          <div>
            <button type="submit" class="btn btn-primary" style="height:38px;">Apply filters</button>
            <?php if ($search !== '' || $courseFilter !== '' || $status !== 'active'): ?>
              <a href="student_management.php" class="btn btn-secondary" style="height:38px;">Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <!-- Add Student Form in Collapsible Card -->
      <details class="card" style="margin-bottom:24px;">
        <summary style="padding:18px 22px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:space-between;user-select:none;">
          <span style="display:flex;align-items:center;gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            Add a new student record
          </span>
          <span class="btn btn-secondary btn-sm">+ Open form</span>
        </summary>
        <div style="padding:0 22px 22px;border-top:1px solid var(--border-subtle);">
          <form method="post" action="student_management.php" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:14px;margin-top:16px;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="add">

            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Student ID</label>
              <input name="student_id" class="form-control" placeholder="e.g. 25CS050" required>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Full name</label>
              <input name="full_name" class="form-control" placeholder="Full legal name" required>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Age</label>
              <input name="age" type="number" min="15" max="100" class="form-control" placeholder="20" required>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Email address</label>
              <input name="email" type="email" class="form-control" placeholder="student@example.edu" required>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Mobile phone</label>
              <input name="phone" class="form-control" placeholder="10-digit number" required>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Gender</label>
              <select name="gender" class="form-select" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Enrolled course</label>
              <select name="course" class="form-select" required>
                <?php foreach ($courses as $c): ?>
                  <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Initial password</label>
              <input name="password" type="password" minlength="8" class="form-control" placeholder="Min 8 characters" required>
            </div>
            <div style="grid-column:1/-1;display:flex;justify-content:flex-end;">
              <button type="submit" class="btn btn-primary">Create student account</button>
            </div>
          </form>
        </div>
      </details>

      <!-- Student Records Table -->
      <section class="card">
        <div class="card-header">
          <div>
            <h2>Enrolled students (<?= count($students) ?>)</h2>
            <p>Showing records matching current active filters</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th scope="col">Student ID</th>
                <th scope="col">Student name</th>
                <th scope="col">Email address</th>
                <th scope="col">Mobile</th>
                <th scope="col">Course</th>
                <th scope="col">Status</th>
                <th scope="col" style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($students)): ?>
                <?php foreach ($students as $s): ?>
                  <tr>
                    <td><strong class="tabular-nums"><?= htmlspecialchars($s['student_id'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($s['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="tabular-nums"><?= htmlspecialchars($s['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($s['course'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= app_format_status_badge((string)$s['status']) ?></td>
                    <td style="text-align:right;">
                      <div style="display:inline-flex;gap:6px;align-items:center;">
                        <details style="position:relative;display:inline-block;">
                          <summary class="btn btn-secondary btn-sm" style="cursor:pointer;user-select:none;">Edit</summary>
                          <div style="position:absolute;right:0;top:100%;z-index:30;width:320px;padding:16px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-card);box-shadow:var(--shadow-lg);margin-top:6px;text-align:left;">
                            <h4 style="margin:0 0 10px;font-size:14px;">Edit: <?= htmlspecialchars($s['student_id'], ENT_QUOTES, 'UTF-8') ?></h4>
                            <form method="post" action="student_management.php">
                              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                              <input type="hidden" name="action" value="update">
                              <input type="hidden" name="student_id" value="<?= htmlspecialchars($s['student_id'], ENT_QUOTES, 'UTF-8') ?>">

                              <div style="margin-bottom:8px;">
                                <label style="font-size:11px;font-weight:600;">Full name</label>
                                <input name="full_name" class="form-control" style="padding:6px 10px;font-size:12px;" value="<?= htmlspecialchars($s['full_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                              </div>
                              <div style="margin-bottom:8px;">
                                <label style="font-size:11px;font-weight:600;">Email</label>
                                <input name="email" type="email" class="form-control" style="padding:6px 10px;font-size:12px;" value="<?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                              </div>
                              <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                                <div>
                                  <label style="font-size:11px;font-weight:600;">Age</label>
                                  <input name="age" type="number" min="15" max="100" class="form-control" style="padding:6px 10px;font-size:12px;" value="<?= htmlspecialchars((string)$s['age'], ENT_QUOTES, 'UTF-8') ?>" required>
                                </div>
                                <div>
                                  <label style="font-size:11px;font-weight:600;">Gender</label>
                                  <input name="gender" class="form-control" style="padding:6px 10px;font-size:12px;" value="<?= htmlspecialchars($s['gender'], ENT_QUOTES, 'UTF-8') ?>" required>
                                </div>
                              </div>
                              <div style="margin-bottom:8px;">
                                <label style="font-size:11px;font-weight:600;">Phone</label>
                                <input name="phone" class="form-control" style="padding:6px 10px;font-size:12px;" value="<?= htmlspecialchars($s['phone'], ENT_QUOTES, 'UTF-8') ?>" required>
                              </div>
                              <div style="margin-bottom:12px;">
                                <label style="font-size:11px;font-weight:600;">Course</label>
                                <select name="course" class="form-select" style="padding:6px 10px;font-size:12px;">
                                  <?php foreach ($courses as $c): ?>
                                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>" <?= $s['course'] === $c ? 'selected' : '' ?>><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                              <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Save changes</button>
                            </form>
                          </div>
                        </details>

                        <?php if ($s['status'] === 'active'): ?>
                          <form method="post" action="student_management.php" onsubmit="return confirm('Are you sure you want to mark this student as inactive?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($s['student_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-danger btn-sm" title="Soft-delete student">Deactivate</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted)">
                    No student records match the specified filters.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
