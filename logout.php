<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/audit.php';
app_no_cache_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !app_csrf_is_valid()) {
    header('Location: login.php', true, 303);
    exit;
}

app_audit_log('admin.logout', 'Administrator signed out');
app_destroy_session();
header('Location: login.php?logged_out=1', true, 303);
exit;
