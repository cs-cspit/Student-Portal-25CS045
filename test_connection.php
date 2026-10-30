<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

$events = [];
$connectionMessage = '';
$connectionReady = false;

try {
    $pdo = app_db();
    $statement = $pdo->prepare(
        'SELECT e.event_id, e.title, e.category, e.event_date, e.start_time, e.location,
                COUNT(r.registration_id) AS registrations
         FROM events AS e
         LEFT JOIN registrations AS r
           ON r.event_id = e.event_id AND r.status = :registration_status
         WHERE e.event_date >= :today
         GROUP BY e.event_id, e.title, e.category, e.event_date, e.start_time, e.location
         ORDER BY e.event_date ASC, e.start_time ASC
         LIMIT :row_limit'
    );
    $statement->bindValue(':registration_status', 'registered', PDO::PARAM_STR);
    $statement->bindValue(':today', date('Y-m-d'), PDO::PARAM_STR);
    $statement->bindValue(':row_limit', 20, PDO::PARAM_INT);
    $statement->execute();
    $events = $statement->fetchAll();
    $connectionReady = true;
    $connectionMessage = 'Connection established. Event data was retrieved with a prepared statement.';
} catch (Throwable $error) {
    http_response_code(503);
    $connectionMessage = $error instanceof RuntimeException
        ? $error->getMessage()
        : 'The database could not be read. Check the connection settings and table setup.';
}

function connection_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Database status · StudentHub</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="connection-page">
  <header class="auth-header"><a class="auth-brand" href="index.php"><span class="brand-mark">S</span><span>StudentHub<small>STUDENT PORTAL</small></span></a><a href="login.php">Student sign in</a></header>
  <main class="connection-main">
    <p class="auth-eyebrow">SYSTEM STATUS</p>
    <h1>Database connection</h1>
    <p class="connection-status <?= $connectionReady ? 'is-connected' : 'is-unavailable' ?>" role="status"><?= connection_escape($connectionMessage) ?></p>
    <?php if ($connectionReady): ?>
      <section class="connection-table-wrap" aria-labelledby="event-data-heading">
        <h2 id="event-data-heading">Upcoming events</h2>
        <?php if ($events): ?>
          <table class="connection-table">
            <thead><tr><th>Event</th><th>Category</th><th>Date</th><th>Location</th><th>Registrations</th></tr></thead>
            <tbody>
              <?php foreach ($events as $event): ?>
                <tr>
                  <td><?= connection_escape((string)$event['title']) ?></td>
                  <td><?= connection_escape((string)$event['category']) ?></td>
                  <td><?= connection_escape((string)$event['event_date']) ?> · <?= connection_escape(substr((string)$event['start_time'], 0, 5)) ?></td>
                  <td><?= connection_escape((string)$event['location']) ?></td>
                  <td><?= (int)$event['registrations'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="connection-empty">The database is connected, but there are no upcoming events yet.</p>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
