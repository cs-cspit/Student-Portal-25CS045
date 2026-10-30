<?php
declare(strict_types=1);
require_once __DIR__ . '/../db.php';

function app_audit_log(string $action, string $details = ''): void
{
    $actor = $_SESSION['student'] ?? [];
    if (($actor['role'] ?? '') !== 'Admin') return;
    try {
        $db = app_mysqli();
        $email = (string)($actor['email'] ?? '');
        $role = 'Admin';
        $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
        $stmt = $db->prepare('INSERT INTO audit_logs (actor_email,actor_role,action,details,ip_address) VALUES (?,?,?,?,?)');
        $stmt->bind_param('sssss', $email, $role, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $error) {
        error_log('StudentHub audit log failed: ' . $error->getMessage());
    }
}
