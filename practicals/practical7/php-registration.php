<?php
declare(strict_types=1);

$errors = [];
$success = '';
$allowedCourses = ['B.Tech', 'BCA', 'B.Sc'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(strip_tags((string)($_POST['name'] ?? '')));
    $email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
    $mobile = preg_replace('/\D+/', '', (string)($_POST['mobile'] ?? '')) ?? '';
    $course = trim(strip_tags((string)($_POST['course'] ?? '')));
    $message = trim(strip_tags((string)($_POST['message'] ?? '')));

    if ($name === '' || strlen($name) > 400) {
        $errors[] = 'Name is required (up to 100 characters).';
    }
    if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $errors[] = 'Enter a valid 10-digit mobile number.';
    }
    if (!in_array($course, $allowedCourses, true)) {
        $errors[] = 'Please select a valid course.';
    }
    if ($message === '' || strlen($message) > 4000) {
        $errors[] = 'Message is required (up to 1000 characters).';
    }

    if (!$errors) {
        $dataDirectory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data';
        if (!is_dir($dataDirectory) && !mkdir($dataDirectory, 0755, true) && !is_dir($dataDirectory)) {
            $errors[] = 'Unable to create the data folder. Please check folder permissions.';
        } else {
            $csvPath = $dataDirectory . DIRECTORY_SEPARATOR . 'registrations.csv';
            $file = @fopen($csvPath, 'c+');
            if ($file === false) {
                $errors[] = 'Unable to save registration. Please check that the data folder is writable.';
            } elseif (!flock($file, LOCK_EX)) {
                fclose($file);
                $errors[] = 'Unable to save registration right now. Please try again.';
            } else {
                fseek($file, 0, SEEK_END);
                if (ftell($file) === 0) {
                    fputcsv($file, ['Name', 'Email', 'Mobile', 'Course', 'Message']);
                }

                $saved = fputcsv($file, [$name, $email, $mobile, $course, $message]);
                fflush($file);
                flock($file, LOCK_UN);
                fclose($file);

                if ($saved === false) {
                    $errors[] = 'Unable to save registration. Please try again.';
                } else {
                    $success = 'Registration submitted successfully.';
                }
            }
        }
    }
} else {
    $errors[] = 'Submit the registration form to continue.';
}

function practical7_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration status · Practical 7</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="form-card result-card">
    <a class="back-link" href="../../index.php">← StudentHub</a>
    <p class="eyebrow">WEB DEVELOPMENT FRAMEWORKS · PRACTICAL 7</p>
    <h1>Registration status</h1>
    <?php if ($success !== ''): ?>
      <p class="success" role="status"><?= practical7_escape($success) ?></p>
      <p class="result-copy">Your valid registration has been appended to the CSV file.</p>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="error" role="alert">
        <strong><?= $success === '' ? 'Please correct the following:' : 'The registration was saved, but an additional issue occurred:' ?></strong>
        <ul><?php foreach ($errors as $error): ?><li><?= practical7_escape($error) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>
    <a class="return-link" href="index.html">Return to the registration form</a>
  </main>
</body>
</html>
