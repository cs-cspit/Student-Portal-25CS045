<?php
declare(strict_types=1);

/**
 * StudentHub database connection.
 * Set STUDENTHUB_DB_HOST, STUDENTHUB_DB_NAME, STUDENTHUB_DB_USER, and
 * STUDENTHUB_DB_PASSWORD in the PHP environment to override local defaults.
 */
function app_db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('STUDENTHUB_DB_HOST') ?: '127.0.0.1';
    $database = getenv('STUDENTHUB_DB_NAME') ?: 'studenthub';
    $username = getenv('STUDENTHUB_DB_USER') ?: 'root';
    $password = getenv('STUDENTHUB_DB_PASSWORD');
    $password = $password === false ? '' : $password;
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $database);

    try {
        $connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
        return $connection;
    } catch (PDOException $error) {
        error_log('StudentHub database connection failed: ' . $error->getMessage());
        throw new RuntimeException('Database connection failed. Check the local database settings and import database/studenthub.sql.', 0, $error);
    }
}

/** MySQLi connection used by Practical 10 authentication. */
function app_mysqli(): mysqli
{
    static $connection = null;
    if ($connection instanceof mysqli) {
        return $connection;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $host = getenv('STUDENTHUB_DB_HOST') ?: '127.0.0.1';
    $database = getenv('STUDENTHUB_DB_NAME') ?: 'studenthub';
    $username = getenv('STUDENTHUB_DB_USER') ?: 'root';
    $password = getenv('STUDENTHUB_DB_PASSWORD');
    $password = $password === false ? '' : $password;

    try {
        $connection = new mysqli($host, $username, $password, $database);
        $connection->set_charset('utf8mb4');
        return $connection;
    } catch (mysqli_sql_exception $error) {
        error_log('StudentHub MySQLi connection failed: ' . $error->getMessage());
        throw new RuntimeException('Authentication database unavailable. Check MySQL and the Practical 10 SQL setup.', 0, $error);
    }
}
