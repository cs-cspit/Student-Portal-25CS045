<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';

app_require_login();
header('Location: ' . app_dashboard_url((string)($_SESSION['student']['role'] ?? 'Student')), true, 303);
exit;
