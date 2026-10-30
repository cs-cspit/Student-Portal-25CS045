<?php
declare(strict_types=1);

function app_base_url(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (preg_match('#/(student|admin|pages|api)/#i', $script)) {
        return '../';
    }
    return '';
}

/**
 * Format raw ISO/MySQL timestamps into readable human date & time.
 * Example: 2026-10-29 19:11:08 -> 29 Oct 2026, 7:11 PM
 */
function app_format_datetime(?string $datetime): string
{
    if (empty($datetime)) {
        return '—';
    }
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8');
    }
    $formatted = date('d M Y, g:i A', $timestamp);
    $rawIso = date('c', $timestamp);
    return sprintf('<time datetime="%s" title="%s" class="tabular-nums">%s</time>',
        htmlspecialchars($rawIso, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8')
    );
}

/**
 * Map raw audit action codes to human readable labels with icons.
 * Example: admin.login -> Signed in
 */
function app_format_audit_action(string $action): array
{
    $map = [
        'admin.login' => ['label' => 'Signed in', 'badge' => 'badge-info', 'icon' => '→'],
        'admin.logout' => ['label' => 'Signed out', 'badge' => 'badge-neutral', 'icon' => '↩'],
        'admin.create' => ['label' => 'Created administrator', 'badge' => 'badge-success', 'icon' => '+'],
        'student.create' => ['label' => 'Added student', 'badge' => 'badge-success', 'icon' => '+'],
        'student.update' => ['label' => 'Updated student', 'badge' => 'badge-info', 'icon' => '✎'],
        'student.soft_delete' => ['label' => 'Deactivated student', 'badge' => 'badge-warning', 'icon' => '✕'],
        'student.delete' => ['label' => 'Deleted student', 'badge' => 'badge-error', 'icon' => '✕'],
        'event.create' => ['label' => 'Created event', 'badge' => 'badge-success', 'icon' => '✳'],
        'event.update' => ['label' => 'Updated event', 'badge' => 'badge-info', 'icon' => '✎'],
        'event.delete' => ['label' => 'Cancelled event', 'badge' => 'badge-warning', 'icon' => '✕'],
        'help.create' => ['label' => 'Submitted help ticket', 'badge' => 'badge-info', 'icon' => '✉'],
        'help.update' => ['label' => 'Updated help ticket', 'badge' => 'badge-info', 'icon' => '✎'],
        'help.resolve' => ['label' => 'Resolved help ticket', 'badge' => 'badge-success', 'icon' => '✓'],
        'help.delete' => ['label' => 'Deleted help ticket', 'badge' => 'badge-error', 'icon' => '✕'],
    ];

    if (isset($map[$action])) {
        return $map[$action];
    }

    $readable = ucwords(str_replace(['.', '_'], ' ', $action));
    return ['label' => $readable, 'badge' => 'badge-neutral', 'icon' => '•'];
}

/**
 * Format status strings into consistent visual badges with dot markers.
 */
function app_format_status_badge(string $status): string
{
    $s = strtolower(trim($status));
    $type = 'badge-neutral';
    $label = ucfirst($s);

    if (in_array($s, ['active', 'registered', 'open', 'completed', 'graded', 'passed'], true)) {
        $type = 'badge-success';
    } elseif (in_array($s, ['pending', 'warning', 'review', 'to do', 'attention'], true)) {
        $type = 'badge-warning';
    } elseif (in_array($s, ['inactive', 'cancelled', 'failed', 'deleted', 'error', 'closed'], true)) {
        $type = 'badge-error';
    }

    return sprintf(
        '<span class="badge %s"><span class="badge-dot"></span>%s</span>',
        $type,
        htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
    );
}

/**
 * Returns initials from a full name (e.g. "Prachi Modi" -> "PM")
 */
function app_get_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: ['User'];
    $first = mb_substr($parts[0] ?? 'U', 0, 1, 'UTF-8');
    $last = count($parts) > 1 ? mb_substr((string)end($parts), 0, 1, 'UTF-8') : '';
    return strtoupper($first . $last);
}

/**
 * Retrieve all help requests and contact applications from data/help_requests.json
 */
function app_get_help_requests(): array
{
    $file = dirname(__DIR__) . '/data/help_requests.json';
    if (!file_exists($file)) {
        return [];
    }
    $content = (string)file_get_contents($file);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

/**
 * Persist a new help or contact request to JSON, CSV (Practical 7), and DB if available.
 */
function app_save_help_request(array $ticket): string
{
    $file = dirname(__DIR__) . '/data/help_requests.json';
    $records = app_get_help_requests();

    $ticketId = 'TICK-26-' . random_int(100, 999);
    $newRecord = [
        'id' => $ticketId,
        'name' => trim((string)($ticket['name'] ?? 'Student')),
        'student_id' => trim((string)($ticket['student_id'] ?? '')),
        'email' => trim((string)($ticket['email'] ?? '')),
        'phone' => trim((string)($ticket['phone'] ?? '')),
        'category' => trim((string)($ticket['category'] ?? 'General Inquiry')),
        'subject' => trim((string)($ticket['subject'] ?? 'Support Inquiry')),
        'message' => trim((string)($ticket['message'] ?? '')),
        'status' => 'open',
        'priority' => trim((string)($ticket['priority'] ?? 'Normal')),
        'source' => trim((string)($ticket['source'] ?? 'Student Portal')),
        'admin_notes' => '',
        'created_at' => date('Y-m-d H:i:s'),
    ];

    // Prepend new request so newest appears first
    array_unshift($records, $newRecord);
    file_put_contents($file, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Also append to data/registrations.csv for Practical 7 alignment
    $csvPath = dirname(__DIR__) . '/data/registrations.csv';
    $fp = @fopen($csvPath, 'a');
    if ($fp !== false) {
        fputcsv($fp, [
            $newRecord['name'],
            $newRecord['email'],
            $newRecord['phone'],
            $newRecord['category'],
            $newRecord['message'],
            $newRecord['created_at'],
        ]);
        fclose($fp);
    }

    return $ticketId;
}

/**
 * Update the status and internal admin notes of an existing help request.
 */
function app_update_help_request(string $ticketId, string $status, string $adminNotes = ''): bool
{
    $file = dirname(__DIR__) . '/data/help_requests.json';
    $records = app_get_help_requests();
    $found = false;

    foreach ($records as &$rec) {
        if (($rec['id'] ?? '') === $ticketId) {
            $rec['status'] = in_array($status, ['open', 'in_progress', 'resolved'], true) ? $status : 'open';
            if ($adminNotes !== '') {
                $rec['admin_notes'] = $adminNotes;
            }
            $rec['updated_at'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    unset($rec);

    if ($found) {
        file_put_contents($file, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    return $found;
}

/**
 * Remove a help request by Ticket ID.
 */
function app_delete_help_request(string $ticketId): bool
{
    $file = dirname(__DIR__) . '/data/help_requests.json';
    $records = app_get_help_requests();
    $filtered = array_values(array_filter($records, static fn($r) => ($r['id'] ?? '') !== $ticketId));

    if (count($filtered) !== count($records)) {
        file_put_contents($file, json_encode($filtered, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}

