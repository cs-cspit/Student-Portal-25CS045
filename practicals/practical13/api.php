<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../db.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

final class EventApiValidationException extends RuntimeException {}

function api_reply(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_SLASHES);
    exit;
}
function save_event_poster(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new EventApiValidationException('Upload failed. Check the server upload limit.');
    if ((int)$file['size'] > 2 * 1024 * 1024) throw new EventApiValidationException('Poster must be 2 MB or smaller.');
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
    $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!isset($types[$mime]) || @getimagesize((string)$file['tmp_name']) === false) throw new EventApiValidationException('Invalid poster type. Use JPG, PNG, or WEBP.');
    $dir=dirname(__DIR__,2).'/uploads/event-posters';
    if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) throw new EventApiValidationException('Poster folder is unavailable.');
    $name=bin2hex(random_bytes(16)).'.'.$types[$mime];
    if (!move_uploaded_file((string)$file['tmp_name'],$dir.'/'.$name)) throw new EventApiValidationException('Could not save poster. Check folder permissions.');
    return 'uploads/event-posters/'.$name;
}

if (empty($_SESSION['student']) || ($_SESSION['student']['role'] ?? '') !== 'Admin') api_reply(['ok'=>false,'message'=>'Unauthorized. Admin access is required.'],401);
try {
    $db=app_mysqli();
    if ($_SERVER['REQUEST_METHOD']==='GET') {
        $rows=$db->query('SELECT event_id,title,category,description,event_date,start_time,end_time,location,capacity,poster_path,status FROM events ORDER BY event_date DESC,start_time DESC')->fetch_all(MYSQLI_ASSOC);
        api_reply(['ok'=>true,'csrf'=>app_csrf_token(),'events'=>$rows]);
    }
    if ($_SERVER['REQUEST_METHOD']!=='POST') api_reply(['ok'=>false,'message'=>'Method not allowed.'],405);
    $token=(string)($_SERVER['HTTP_X_CSRF_TOKEN']??'');
    if (empty($_SESSION['csrf_token']) || !hash_equals((string)$_SESSION['csrf_token'],$token)) api_reply(['ok'=>false,'message'=>'Session expired. Reload the page and retry.'],403);
    $action=(string)($_POST['action']??''); $id=(int)($_POST['event_id']??0);
    if ($action==='delete' && $id>0) {
        $stmt=$db->prepare("UPDATE events SET status='cancelled' WHERE event_id=?"); $stmt->bind_param('i',$id); $stmt->execute();
        if (!$stmt->affected_rows) api_reply(['ok'=>false,'message'=>'Event not found or already cancelled.'],404);
        app_audit_log('event.delete', 'Event ID: ' . $id);
        api_reply(['ok'=>true,'message'=>'Event deleted from the active list.']);
    }
    if (!in_array($action,['create','update'],true)) api_reply(['ok'=>false,'message'=>'Invalid event action.'],422);
    $title=trim((string)($_POST['title']??'')); $category=trim((string)($_POST['category']??''));
    $description=trim((string)($_POST['description']??'')); $date=(string)($_POST['event_date']??'');
    $start=(string)($_POST['start_time']??''); $end=trim((string)($_POST['end_time']??''));
    $location=trim((string)($_POST['location']??'')); $capacityText=trim((string)($_POST['capacity']??''));
    $dateObj=DateTime::createFromFormat('Y-m-d',$date);
    if ($title==='' || mb_strlen($title)>140 || $category==='' || $description==='' || $location==='' || !$dateObj || $dateObj->format('Y-m-d')!==$date || !preg_match('/^\d{2}:\d{2}$/',$start) || ($end!==''&&!preg_match('/^\d{2}:\d{2}$/',$end)) || ($capacityText!==''&&(!ctype_digit($capacityText)||(int)$capacityText>65535))) api_reply(['ok'=>false,'message'=>'Complete the event details with valid date, time, and capacity.'],422);
    $capacity=$capacityText===''?null:(int)$capacityText; $endValue=$end===''?null:$end;
    $poster=save_event_poster($_FILES['poster']??[]);
    if ($action==='create') {
        $stmt=$db->prepare("INSERT INTO events (title,category,description,event_date,start_time,end_time,location,capacity,poster_path,status) VALUES (?,?,?,?,?,?,?,?,?,'open')");
        $stmt->bind_param('sssssssis',$title,$category,$description,$date,$start,$endValue,$location,$capacity,$poster); $stmt->execute();
        app_audit_log('event.create', 'Event: ' . $title);
        api_reply(['ok'=>true,'message'=>'Event created successfully.']);
    }
    if ($id<1) api_reply(['ok'=>false,'message'=>'Select a valid event to update.'],422);
    if ($poster!==null) {
        $stmt=$db->prepare('UPDATE events SET title=?,category=?,description=?,event_date=?,start_time=?,end_time=?,location=?,capacity=?,poster_path=? WHERE event_id=?');
        $stmt->bind_param('sssssssisi',$title,$category,$description,$date,$start,$endValue,$location,$capacity,$poster,$id);
    } else {
        $stmt=$db->prepare('UPDATE events SET title=?,category=?,description=?,event_date=?,start_time=?,end_time=?,location=?,capacity=? WHERE event_id=?');
        $stmt->bind_param('sssssssii',$title,$category,$description,$date,$start,$endValue,$location,$capacity,$id);
    }
    $stmt->execute();
    app_audit_log('event.update', 'Event ID: ' . $id);
    api_reply(['ok'=>true,'message'=>'Event updated successfully.']);
} catch (EventApiValidationException $error) {
    api_reply(['ok'=>false,'message'=>$error->getMessage()],422);
} catch (Throwable $error) {
    error_log('StudentHub event API failed: '.$error->getMessage());
    api_reply(['ok'=>false,'message'=>'Event operation failed. Check the database setup.'],500);
}

