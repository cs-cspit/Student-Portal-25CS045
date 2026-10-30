<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/db.php';
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
            $stmt->bind_param('s', $id); $stmt->execute();
            $message = $stmt->affected_rows ? 'Student marked inactive successfully.' : 'No student record was changed.';
            if ($stmt->affected_rows) app_audit_log('student.soft_delete', 'Student ID: ' . $id);
            $stmt->close();
        } elseif (in_array($action, ['add', 'update'], true)) {
            $name = trim((string)($_POST['full_name'] ?? ''));
            $age = filter_var($_POST['age'] ?? null, FILTER_VALIDATE_INT);
            $email = strtolower(trim((string)($_POST['email'] ?? '')));
            $phone = trim((string)($_POST['phone'] ?? ''));
            $gender = trim((string)($_POST['gender'] ?? ''));
            $course = trim((string)($_POST['course'] ?? ''));
            if ($id === '' || $name === '' || !$age || $age < 15 || $age > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $gender === '' || !in_array($course, $courses, true)) {
                $error = 'Check all student details and try again.';
            } elseif ($action === 'add') {
                $password = (string)($_POST['password'] ?? '');
                if (strlen($password) < 8) $error = 'Set a password of at least 8 characters.';
                else {
                    try {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO students (student_id,full_name,age,email,phone,gender,course,password_hash,role,status) VALUES (?,?,?,?,?,?,?,?,'Student','active')");
                        $stmt->bind_param('ssisssss', $id, $name, $age, $email, $phone, $gender, $course, $hash);
                        $stmt->execute(); $stmt->close(); $message = 'Student added successfully.';
                        app_audit_log('student.create', 'Student ID: ' . $id);
                    } catch (mysqli_sql_exception $e) {
                        $error = $e->getCode() === 1062 ? 'Student ID or email already exists.' : 'Student could not be added.';
                        error_log('StudentHub CRUD insert: ' . $e->getMessage());
                    }
                }
            } else {
                try {
                    $stmt = $db->prepare("UPDATE students SET full_name=?,age=?,email=?,phone=?,gender=?,course=? WHERE student_id=? AND role='Student' AND status='active'");
                    $stmt->bind_param('sisssss', $name, $age, $email, $phone, $gender, $course, $id);
                    $stmt->execute(); $stmt->close(); $message = 'Student details updated successfully.';
                    app_audit_log('student.update', 'Student ID: ' . $id);
                } catch (mysqli_sql_exception $e) {
                    $error = $e->getCode() === 1062 ? 'That email is already in use.' : 'Student could not be updated.';
                    error_log('StudentHub CRUD update: ' . $e->getMessage());
                }
            }
        }
    }
    $_SESSION['manage_notice'] = $error !== '' ? $error : $message;
    header('Location: student_management.php', true, 303); exit;
}

$search = trim((string)($_GET['search'] ?? ''));
$courseFilter = trim((string)($_GET['course'] ?? ''));
$status = in_array($_GET['status'] ?? '', ['active','inactive'], true) ? (string)$_GET['status'] : 'active';
$where = "role='Student' AND status=? AND (student_id LIKE ? OR full_name LIKE ? OR email LIKE ?)";
$like = '%' . $search . '%';
if (isset($_GET['export'])) {
    $sql = "SELECT student_id,full_name,age,email,phone,gender,course,status FROM students WHERE $where" . (in_array($courseFilter,$courses,true) ? ' AND course=?' : '') . ' ORDER BY full_name';
    $stmt=$db->prepare($sql);
    if (in_array($courseFilter,$courses,true)) $stmt->bind_param('sssss',$status,$like,$like,$like,$courseFilter); else $stmt->bind_param('ssss',$status,$like,$like,$like);
    $stmt->execute(); $result=$stmt->get_result();
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="studenthub-students.csv"');
    $out=fopen('php://output','w');
    $savedFile=fopen(__DIR__ . '/data/student_records.csv','w');
    $headers=['Student ID','Name','Age','Email','Phone','Gender','Course','Status'];
    fputcsv($out,$headers);
    if ($savedFile !== false) fputcsv($savedFile,$headers);
    while ($row=$result->fetch_assoc()) {
        $values=array_values($row);
        fputcsv($out,$values);
        if ($savedFile !== false) fputcsv($savedFile,$values);
    }
    fclose($out);
    if ($savedFile !== false) fclose($savedFile);
    $stmt->close(); exit;
}
$sql = "SELECT student_id,full_name,age,email,phone,gender,course,status FROM students WHERE $where" . (in_array($courseFilter,$courses,true) ? ' AND course=?' : '') . ' ORDER BY full_name';
$stmt=$db->prepare($sql);
if (in_array($courseFilter,$courses,true)) $stmt->bind_param('sssss',$status,$like,$like,$like,$courseFilter); else $stmt->bind_param('ssss',$status,$like,$like,$like);
$stmt->execute(); $students=$stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Student management · StudentHub</title><link rel="stylesheet" href="css/studyhub.css"><link rel="stylesheet" href="practicals/practical10/style.css"><style>
.crud-form,.filters{display:flex;flex-wrap:wrap;gap:10px;align-items:end;margin:18px 0;padding:16px;background:#fff;border:1px solid #e0e7ec;border-radius:10px}.crud-form label,.filters label{display:grid;gap:5px;font-size:11px}.crud-form input,.crud-form select,.filters input,.filters select{padding:9px;border:1px solid #ccd7df;border-radius:6px}.crud-form button,.filters button,.export{padding:10px 13px;border:0;border-radius:6px;background:#176b67;color:#fff;text-decoration:none;cursor:pointer}.grid{overflow:auto;background:white;border:1px solid #e0e7ec;border-radius:9px}.grid table{width:100%;border-collapse:collapse;font-size:11px}.grid th,.grid td{padding:10px;border-bottom:1px solid #e7edf1;text-align:left;white-space:nowrap}.grid th{background:#f4f7fa}.notice{padding:12px;margin:12px 0;border-radius:6px;background:#e8f5ef;color:#205f45}.notice.error{background:#fff0ee;color:#913d35}.inline{display:flex;gap:4px}.inline input,.inline select{width:105px;padding:5px}.inline button,.del{border:0;border-radius:5px;padding:6px;background:#176b67;color:#fff;cursor:pointer}.del{background:#9b3f42}
</style></head><body class="admin-page"><div class="admin-shell"><aside class="admin-rail"><a class="admin-brand" href="admin_dashboard.php"><span class="brand-mark">S</span><span>StudentHub<small>ADMINISTRATION</small></span></a><p class="admin-nav-label">ADMINISTRATION</p><a class="admin-nav-link" href="admin_dashboard.php">▦ &nbsp; Overview</a><a class="admin-nav-link is-active" href="student_management.php">♙ &nbsp; Students</a><a class="admin-nav-link" href="admin_register.php">＋ &nbsp; Add administrator</a><div class="admin-rail-bottom"><span class="admin-role-pill">Administrator</span><form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>"><button class="admin-logout">Log out</button></form></div></aside><main class="admin-main"><header class="admin-topbar"><div><p class="admin-eyebrow">STUDENTHUB · RECORDS</p><h1>Student management</h1></div></header>
<?php if($message!==''): ?><div class="notice <?= str_contains(strtolower($message),'could not')||str_contains(strtolower($message),'already')||str_contains(strtolower($message),'check')||str_contains(strtolower($message),'expired')?'error':'' ?>" role="status"><?= app_escape($message) ?></div><?php endif; ?>
<form class="crud-form" method="post"><input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>"><input type="hidden" name="action" value="add"><strong style="width:100%">Add student record</strong><label>Student ID<input name="student_id" required></label><label>Name<input name="full_name" required></label><label>Age<input name="age" type="number" min="15" max="100" required></label><label>Email<input name="email" type="email" required></label><label>Phone<input name="phone" required></label><label>Gender<input name="gender" required></label><label>Course<select name="course"><?php foreach($courses as $c): ?><option><?= app_escape($c) ?></option><?php endforeach; ?></select></label><label>Initial password<input name="password" type="password" minlength="8" required></label><button>Add student</button></form>
<form class="filters" method="get"><label>Search<input name="search" value="<?= app_escape($search) ?>" placeholder="Name, ID, or email"></label><label>Course<select name="course"><option value="">All courses</option><?php foreach($courses as $c): ?><option value="<?= app_escape($c) ?>" <?= $courseFilter===$c?'selected':'' ?>><?= app_escape($c) ?></option><?php endforeach; ?></select></label><label>Status<select name="status"><option value="active" <?= $status==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option></select></label><button>Search / filter</button><a class="export" href="?<?= http_build_query(['export'=>1,'search'=>$search,'course'=>$courseFilter,'status'=>$status]) ?>">Export CSV</a></form>
<div class="grid"><table><thead><tr><th>ID</th><th>Name</th><th>Age</th><th>Email</th><th>Phone</th><th>Gender</th><th>Course</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($students as $s): ?><tr><td><?= app_escape($s['student_id']) ?></td><td><?= app_escape($s['full_name']) ?></td><td><?= app_escape((string)$s['age']) ?></td><td><?= app_escape($s['email']) ?></td><td><?= app_escape($s['phone']) ?></td><td><?= app_escape($s['gender']) ?></td><td><?= app_escape($s['course']) ?></td><td><?= app_escape($s['status']) ?></td><td><details><summary>Edit</summary><form class="inline" method="post"><input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="student_id" value="<?= app_escape($s['student_id']) ?>"><input name="full_name" value="<?= app_escape($s['full_name']) ?>" required><input name="age" type="number" min="15" max="100" value="<?= app_escape((string)$s['age']) ?>" required><input name="email" type="email" value="<?= app_escape($s['email']) ?>" required><input name="phone" value="<?= app_escape($s['phone']) ?>" required><input name="gender" value="<?= app_escape($s['gender']) ?>" required><select name="course"><?php foreach($courses as $c): ?><option <?= $s['course']===$c?'selected':'' ?>><?= app_escape($c) ?></option><?php endforeach; ?></select><button>Save</button></form></details><form method="post" onsubmit="return confirm('Mark this student inactive?')"><input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="student_id" value="<?= app_escape($s['student_id']) ?>"><button class="del">Delete</button></form></td></tr><?php endforeach; ?><?php if(!$students): ?><tr><td colspan="9">No records match these filters.</td></tr><?php endif; ?></tbody></table></div></main></div></body></html>
