<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/db.php';

app_no_cache_headers();
if (!empty($_SESSION['student'])) {
    header('Location: dashboard.php', true, 303);
    exit;
}
$errors = [];
$values = ['name' => '', 'age' => '', 'student_id' => '', 'email' => '', 'phone' => '', 'gender' => '', 'course' => ''];
$genders = ['Female', 'Male', 'Non-binary', 'Prefer not to say'];
$courses = ['B.Tech', 'BCA', 'B.Sc Computer Science', 'MCA'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $values['email'] = strtolower($values['email']);
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if (!app_csrf_is_valid()) {
        $errors[] = 'Your form session expired. Please review your details and submit again.';
    }
    foreach (array_merge($values, ['password' => $password, 'confirm_password' => $confirmPassword]) as $value) {
        if ($value === '') {
            $errors[] = 'Complete every required field before submitting the form.';
            break;
        }
    }
    if ($values['name'] === '' || mb_strlen($values['name']) > 100) {
        $errors[] = 'Enter your full name (up to 100 characters).';
    }
    if (!ctype_digit($values['age']) || (int)$values['age'] < 15 || (int)$values['age'] > 100) {
        $errors[] = 'Enter a valid age between 15 and 100.';
    }
    if (!preg_match('/^[A-Za-z0-9\/-]{3,30}$/', $values['student_id'])) {
        $errors[] = 'Enter a valid student ID using 3–30 letters, numbers, hyphens, or slashes.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 254) {
        $errors[] = 'Enter a valid college or registered personal email address.';
    }
    if (!preg_match('/^\+?[0-9][0-9() .-]{6,18}$/', $values['phone'])) {
        $errors[] = 'Enter a valid phone number.';
    }
    if (!in_array($values['gender'], $genders, true)) {
        $errors[] = 'Choose a gender option.';
    }
    if (!in_array($values['course'], $courses, true)) {
        $errors[] = 'Choose the course you are enrolled in.';
    }
    if (strlen($password) < 8 || strlen($password) > 200) {
        $errors[] = 'Create a password with at least 8 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'The password and confirmation do not match.';
    }

    if (!$errors) {
        $csvPath = app_accounts_path();
        $file = fopen($csvPath, 'c+');
        if ($file === false || !flock($file, LOCK_EX)) {
            if (is_resource($file)) {
                fclose($file);
            }
            $errors[] = 'We could not save your account. Check that the data folder is writable and try again.';
        } else {
            rewind($file);
            $header = fgetcsv($file);
            $duplicateStudentId = false;
            $duplicateEmail = false;
            if (is_array($header)) {
                while (($row = fgetcsv($file)) !== false) {
                    if (count($row) < 5) {
                        continue;
                    }
                    $duplicateStudentId = $duplicateStudentId || strcasecmp($row[0], $values['student_id']) === 0;
                    $duplicateEmail = $duplicateEmail || strcasecmp($row[3], $values['email']) === 0;
                }
            }

            if ($duplicateStudentId) {
                $errors[] = 'That student ID is already registered. Log in or check the ID.';
            }
            if ($duplicateEmail) {
                $errors[] = 'That email address is already registered. Log in or use another email.';
            }

            $pdo = null;
            if (!$errors) {
                try {
                    $pdo = app_db();
                    $duplicateCheck = $pdo->prepare(
                        'SELECT student_id, email FROM students
                         WHERE student_id = :student_id OR email = :email LIMIT 1'
                    );
                    $duplicateCheck->execute([
                        ':student_id' => $values['student_id'],
                        ':email' => $values['email'],
                    ]);
                    $existingStudent = $duplicateCheck->fetch();
                    if ($existingStudent !== false) {
                        if (strcasecmp((string)$existingStudent['student_id'], $values['student_id']) === 0) {
                            $errors[] = 'That student ID is already registered. Log in or check the ID.';
                        }
                        if (strcasecmp((string)$existingStudent['email'], $values['email']) === 0) {
                            $errors[] = 'That email address is already registered. Log in or use another email.';
                        }
                    }
                } catch (Throwable $error) {
                    error_log('StudentHub registration duplicate check failed: ' . $error->getMessage());
                    $errors[] = 'We could not check your account details in the database. Confirm MySQL is running and apply the Practical 9 database migration.';
                }
            }

            if (!$errors && $pdo instanceof PDO) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $originalSize = null;
                $csvWriteStarted = false;
                try {
                    $pdo->beginTransaction();
                    $insertStudent = $pdo->prepare(
                        'INSERT INTO students
                         (student_id, full_name, age, email, phone, gender, course, password_hash)
                         VALUES (:student_id, :full_name, :age, :email, :phone, :gender, :course, :password_hash)'
                    );
                    $insertStudent->execute([
                        ':student_id' => $values['student_id'],
                        ':full_name' => $values['name'],
                        ':age' => (int)$values['age'],
                        ':email' => $values['email'],
                        ':phone' => $values['phone'],
                        ':gender' => $values['gender'],
                        ':course' => $values['course'],
                        ':password_hash' => $passwordHash,
                    ]);

                    fseek($file, 0, SEEK_END);
                    $originalSize = ftell($file);
                    $csvWriteStarted = true;
                    if ($originalSize === 0 && fputcsv($file, ['Student ID', 'Name', 'Age', 'Email', 'Phone', 'Gender', 'Course', 'Password Hash', 'Registered At']) === false) {
                        throw new RuntimeException('Could not write the accounts CSV header.');
                    }
                    $saved = fputcsv($file, [
                        $values['student_id'], $values['name'], $values['age'], $values['email'],
                        $values['phone'], $values['gender'], $values['course'],
                        $passwordHash, date(DATE_ATOM),
                    ]);
                    if ($saved === false || !fflush($file)) {
                        throw new RuntimeException('Could not write the new account to the accounts CSV file.');
                    }
                    $pdo->commit();
                } catch (Throwable $error) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    if ($csvWriteStarted && $originalSize !== null) {
                        ftruncate($file, $originalSize);
                        fflush($file);
                    }
                    if ($error instanceof PDOException && $error->getCode() === '23000') {
                        $errors[] = 'That student ID or email address is already registered. Log in or review your details.';
                    } else {
                        error_log('StudentHub account registration failed: ' . $error->getMessage());
                        $errors[] = 'We could not save your account to the database and CSV file. Check the database migration and try again.';
                    }
                }
            }
            flock($file, LOCK_UN);
            fclose($file);
        }
    }

    if (!$errors) {
        $_SESSION['registration_notice'] = 'Your StudentHub account has been created. Log in with your student ID or email and password.';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: login.php?registered=1', true, 303);
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create account · StudentHub</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">
  <header class="auth-header"><a class="auth-brand" href="index.php"><span class="brand-mark">S</span><span>StudentHub<small>STUDENT PORTAL</small></span></a><p>Already registered? <a href="login.php">Log in</a></p></header>
  <main class="auth-main auth-main-wide">
    <section class="auth-intro"><p class="auth-eyebrow">STUDENT ACCOUNT</p><h1>Create your account</h1><p>Register with your student details to access your academic workspace.</p><div class="auth-step"><span>01</span><div><strong>Your student identity</strong><small>Use the details associated with your college.</small></div></div><div class="auth-step"><span>02</span><div><strong>Secure sign-in</strong><small>After registration, log in using your student ID or email.</small></div></div></section>
    <section class="auth-card">
      <div class="auth-card-heading"><h2>Registration details</h2><p>All fields are required.</p></div>
      <?php if ($errors): ?><div class="form-alert form-alert-error" role="alert"><strong>Please review the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= app_escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
      <form method="post" action="register.php" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= app_escape(app_csrf_token()) ?>">
        <div class="form-grid">
          <div class="field"><label for="name">Full name</label><input id="name" name="name" type="text" autocomplete="name" maxlength="100" value="<?= app_escape($values['name']) ?>" required></div>
          <div class="field"><label for="age">Age</label><input id="age" name="age" type="number" min="15" max="100" inputmode="numeric" value="<?= app_escape($values['age']) ?>" required></div>
          <div class="field"><label for="student_id">Student ID</label><input id="student_id" name="student_id" type="text" autocomplete="off" maxlength="30" value="<?= app_escape($values['student_id']) ?>" required></div>
          <div class="field"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="20" value="<?= app_escape($values['phone']) ?>" required></div>
          <div class="field field-full"><label for="email">College email ID</label><input id="email" name="email" type="email" autocomplete="email" maxlength="254" value="<?= app_escape($values['email']) ?>" aria-describedby="email-note" required><small class="field-note" id="email-note">If you have not received your college email yet, enter the personal email address registered with your college.</small></div>
          <fieldset class="field"><legend>Gender</legend><select id="gender" name="gender" required><option value="">Select an option</option><?php foreach ($genders as $gender): ?><option value="<?= app_escape($gender) ?>" <?= $values['gender'] === $gender ? 'selected' : '' ?>><?= app_escape($gender) ?></option><?php endforeach; ?></select></fieldset>
          <div class="field"><label for="course">Enrolled course</label><select id="course" name="course" required><option value="">Select your course</option><?php foreach ($courses as $course): ?><option value="<?= app_escape($course) ?>" <?= $values['course'] === $course ? 'selected' : '' ?>><?= app_escape($course) ?></option><?php endforeach; ?></select></div>
          <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required><small class="field-note">Use at least 8 characters.</small></div>
          <div class="field"><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" minlength="8" required></div>
        </div>
        <button class="auth-submit" type="submit">Create account <span aria-hidden="true">→</span></button>
      </form>
      <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
    </section>
  </main>
  <footer class="auth-footer">StudentHub · Student account access</footer>
</body>
</html>
