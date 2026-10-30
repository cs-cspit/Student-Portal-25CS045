<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
app_no_cache_headers();

if (!isset($_GET['expired'])) {
    header('Location: login.php', true, 303);
    exit;
}
$_SESSION['login_notice'] = 'Your session expired due to inactivity. Please sign in again.';
header('Location: login.php?timeout=1', true, 303);
exit;
