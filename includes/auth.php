<?php
declare(strict_types=1);

const APP_SESSION_TIMEOUT = 900;

function app_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.gc_maxlifetime', (string)APP_SESSION_TIMEOUT);
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function app_no_cache_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function app_require_login(): void
{
    app_no_cache_headers();
    if (empty($_SESSION['student']) || !is_array($_SESSION['student'])) {
        header('Location: login.php', true, 303);
        exit;
    }

    $lastActivity = (int)($_SESSION['last_activity'] ?? 0);
    if ($lastActivity === 0 || time() - $lastActivity > APP_SESSION_TIMEOUT) {
        app_destroy_session();
        header('Location: timeout.php?expired=1', true, 303);
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function app_require_role(string $role): void
{
    app_require_login();
    if (($_SESSION['student']['role'] ?? 'Student') !== $role) {
        http_response_code(403);
        require __DIR__ . '/../unauthorized.php';
        exit;
    }
}

function app_dashboard_url(string $role): string
{
    return $role === 'Admin' ? 'admin_dashboard.php' : 'student_dashboard.php';
}

function app_destroy_session(): void
{
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $cookie['path'],
            'domain' => $cookie['domain'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => $cookie['samesite'] ?? 'Lax',
        ]);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function app_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function app_csrf_is_valid(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && is_string($_POST['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function app_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_accounts_path(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'accounts.csv';
}

app_start_session();
