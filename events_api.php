<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/db.php';

app_require_login();
header('Content-Type: application/json; charset=utf-8');

function events_api_reply(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_SLASHES);
    exit;
}

$student = $_SESSION['student'];
$studentId = (string)($student['student_id'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $statement = app_db()->prepare(
            "SELECT e.event_id, e.title, e.category, e.description, e.event_date,
                    e.start_time, e.location, e.capacity,
                    (SELECT COUNT(*) FROM registrations AS total
                     WHERE total.event_id = e.event_id AND total.status = 'registered') AS registrations,
                    (SELECT COUNT(*) FROM registrations AS mine
                     WHERE mine.event_id = e.event_id AND mine.student_id = :student_id
                       AND mine.status = 'registered') AS is_registered
             FROM events AS e
             WHERE e.event_date >= CURRENT_DATE AND e.status = 'open'
             ORDER BY e.event_date ASC, e.start_time ASC"
        );
        $statement->execute(['student_id' => $studentId]);
        events_api_reply(['ok' => true, 'events' => $statement->fetchAll()]);
    } catch (Throwable $error) {
        error_log('StudentHub events could not be loaded: ' . $error->getMessage());
        events_api_reply(['ok' => false, 'message' => 'Event data is temporarily unavailable.'], 503);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: GET, POST');
    events_api_reply(['ok' => false, 'message' => 'This request is not supported.'], 405);
}

if (!app_csrf_is_valid()) {
    events_api_reply(['ok' => false, 'message' => 'Your session expired. Refresh the page and try again.'], 403);
}

$eventId = filter_var($_POST['event_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($eventId === false || $eventId === null || $studentId === '') {
    events_api_reply(['ok' => false, 'message' => 'The event could not be identified.'], 422);
}

try {
    $pdo = app_db();
    $pdo->beginTransaction();

    // Locking the event row keeps capacity checks safe when registrations arrive together.
    $eventStatement = $pdo->prepare(
        "SELECT event_id, capacity FROM events
         WHERE event_id = :event_id AND event_date >= CURRENT_DATE AND status = 'open'
         FOR UPDATE"
    );
    $eventStatement->execute(['event_id' => $eventId]);
    $event = $eventStatement->fetch();
    if (!$event) {
        $pdo->rollBack();
        events_api_reply(['ok' => false, 'message' => 'This event is no longer open for registration.'], 404);
    }

    $duplicateStatement = $pdo->prepare(
        "SELECT registration_id FROM registrations
         WHERE student_id = :student_id AND event_id = :event_id AND status = 'registered'
         LIMIT 1"
    );
    $duplicateStatement->execute(['student_id' => $studentId, 'event_id' => $eventId]);
    if ($duplicateStatement->fetch()) {
        $pdo->rollBack();
        events_api_reply(['ok' => false, 'message' => 'You are already registered for this event.'], 409);
    }

    $countStatement = $pdo->prepare(
        "SELECT COUNT(*) FROM registrations WHERE event_id = :event_id AND status = 'registered'"
    );
    $countStatement->execute(['event_id' => $eventId]);
    $registeredCount = (int)$countStatement->fetchColumn();
    if ($event['capacity'] !== null && $registeredCount >= (int)$event['capacity']) {
        $pdo->rollBack();
        events_api_reply(['ok' => false, 'message' => 'This event has reached its registration limit.'], 409);
    }

    $studentStatement = $pdo->prepare(
        'INSERT INTO students (student_id, full_name, age, email, phone, gender, course)
         VALUES (:student_id, :full_name, :age, :email, :phone, :gender, :course)
         ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), age = VALUES(age),
           email = VALUES(email), phone = VALUES(phone),
           gender = VALUES(gender), course = VALUES(course)'
    );
    $studentStatement->execute([
        'student_id' => $studentId,
        'full_name' => (string)($student['name'] ?? 'Student'),
        'age' => max(15, min(100, (int)($student['age'] ?? 18))),
        'email' => (string)($student['email'] ?? ''),
        'phone' => (string)($student['phone'] ?? ''),
        'gender' => (string)($student['gender'] ?? 'Prefer not to say'),
        'course' => (string)($student['course'] ?? 'Not specified'),
    ]);

    $insertStatement = $pdo->prepare(
        "INSERT INTO registrations (student_id, event_id, status)
         VALUES (:student_id, :event_id, 'registered')"
    );
    $insertStatement->execute(['student_id' => $studentId, 'event_id' => $eventId]);
    $pdo->commit();
    events_api_reply(['ok' => true, 'message' => 'Your place is confirmed.']);
} catch (Throwable $error) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('StudentHub event registration failed: ' . $error->getMessage());
    if ($error instanceof PDOException && (string)$error->getCode() === '23000') {
        events_api_reply(['ok' => false, 'message' => 'Your account details conflict with an existing database record. Please contact support.'], 409);
    }
    events_api_reply(['ok' => false, 'message' => 'Registration could not be completed. Check the database setup and try again.'], 503);
}
