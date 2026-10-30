<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/db.php';
app_no_cache_headers();

function app_find_csv_account(string $identifier): ?array
{
    $file = @fopen(app_accounts_path(), 'rb');
    if ($file === false) {
        return null;
    }
    $account = null;
    if (flock($file, LOCK_SH)) {
        fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 8 && (strcasecmp($row[0], $identifier) === 0 || strcasecmp($row[3], $identifier) === 0)) {
                $account = [
                    'student_id' => $row[0], 'full_name' => $row[1], 'age' => $row[2],
                    'email' => $row[3], 'phone' => $row[4], 'gender' => $row[5],
                    'course' => $row[6], 'password_hash' => $row[7], 'role' => 'Student',
                ];
                break;
            }
        }
        flock($file, LOCK_UN);
    }
    fclose($file);
    return $account;
}

function app_lookup_database_account(string $identifier): ?array
{
    $mysqli = app_mysqli();
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $statement = $mysqli->prepare(
            'SELECT student_id, full_name, age, email, phone, gender, course, password_hash, role
             FROM students WHERE email = ? LIMIT 1'
        );
    } else {
        $statement = $mysqli->prepare(
            'SELECT student_id, full_name, age, email, phone, gender, course, password_hash, role
             FROM students WHERE student_id = ? LIMIT 1'
        );
    }
    $statement->bind_param('s', $identifier);
    $statement->execute();
    $account = $statement->get_result()->fetch_assoc() ?: null;
    $statement->close();

    // Administrators have their own table and do not need student-only fields.
    if ($account === null && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $statement = $mysqli->prepare(
            'SELECT admin_id, full_name, email, password_hash
             FROM admins WHERE email = ? LIMIT 1'
        );
        $statement->bind_param('s', $identifier);
        $statement->execute();
        $admin = $statement->get_result()->fetch_assoc() ?: null;
        $statement->close();
        if ($admin !== null) {
            $account = [
                'student_id' => '', 'full_name' => $admin['full_name'],
                'age' => '', 'email' => $admin['email'], 'phone' => '',
                'gender' => '', 'course' => '', 'password_hash' => $admin['password_hash'],
                'role' => 'Admin',
            ];
        }
    }
    return $account;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php', true, 303);
    exit;
}

$identifier = trim((string)($_POST['identifier'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$captcha = trim((string)($_POST['captcha'] ?? ''));
$expectedCaptcha = (int)($_SESSION['captcha_a'] ?? 0) + (int)($_SESSION['captcha_b'] ?? 0);
$errorMessage = '';

if (!app_csrf_is_valid()) {
    $errorMessage = 'Your sign-in form expired. Please try again.';
} elseif ($identifier === '' || $password === '' || $captcha === '') {
    $errorMessage = 'Enter your email or student ID, password, and security-check answer.';
} elseif (!ctype_digit($captcha) || (int)$captcha !== $expectedCaptcha) {
    $errorMessage = 'The security-check answer is incorrect. Please try again.';
} else {
    $account = null;
    try {
        $account = app_lookup_database_account($identifier);
    } catch (Throwable $error) {
        error_log('StudentHub login database lookup failed: ' . $error->getMessage());
    }

    // Existing Practical 9 CSV accounts remain usable and default to Student.
    if ($account === null) {
        $account = app_find_csv_account($identifier);
    }

    if ($account !== null
        && !empty($account['password_hash'])
        && password_verify($password, (string)$account['password_hash'])
        && in_array((string)($account['role'] ?? 'Student'), ['Student', 'Admin'], true)) {
        session_regenerate_id(true);
        $_SESSION['student'] = [
            'student_id' => (string)$account['student_id'],
            'name' => (string)$account['full_name'],
            'age' => (string)$account['age'],
            'email' => (string)$account['email'],
            'phone' => (string)$account['phone'],
            'gender' => (string)$account['gender'],
            'course' => (string)$account['course'],
            'role' => (string)($account['role'] ?? 'Student'),
        ];
        $_SESSION['last_activity'] = time();
        $_SESSION['authenticated_at'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        if ((string)$_SESSION['student']['role'] === 'Admin') app_audit_log('admin.login', 'Successful administrator login');
        unset($_SESSION['captcha_a'], $_SESSION['captcha_b'], $_SESSION['login_errors'], $_SESSION['login_notice']);
        header('Location: ' . app_dashboard_url((string)$_SESSION['student']['role']), true, 303);
        exit;
    }
    $errorMessage = 'The email/student ID or password is incorrect.';
}

$_SESSION['login_errors'] = [$errorMessage];
$_SESSION['captcha_a'] = random_int(2, 9);
$_SESSION['captcha_b'] = random_int(1, 9);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header('Location: login.php', true, 303);
exit;
