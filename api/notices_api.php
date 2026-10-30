<?php
declare(strict_types=1);
$root = dirname(__DIR__);
require_once $root . '/includes/auth.php';

app_require_login();
app_no_cache_headers();

header('Content-Type: application/json; charset=utf-8');

$noticesFile = $root . '/data/notices.json';
$notices = [];
if (file_exists($noticesFile)) {
    $notices = json_decode((string)file_get_contents($noticesFile), true) ?: [];
}

echo json_encode([
    'success' => true,
    'notices' => $notices
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


