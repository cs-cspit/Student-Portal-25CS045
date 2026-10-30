<?php
declare(strict_types=1);

function practical13_db(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $host = getenv('STUDENTHUB_DB_HOST') ?: '127.0.0.1';
    $database = getenv('STUDENTHUB_DB_NAME') ?: 'studenthub';
    $user = getenv('STUDENTHUB_DB_USER') ?: 'root';
    $password = getenv('STUDENTHUB_DB_PASSWORD') ?: '';
    $db = new mysqli($host, $user, $password, $database);
    $db->set_charset('utf8mb4');
    return $db;
}

