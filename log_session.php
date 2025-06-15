<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

require_once 'db.php'; 

$input = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'];
$taskId = $input['task_id'] ?? null;
$sessionType = $input['session_type'] ?? null;
$duration = (int)($input['duration'] ?? 0);
$customWork = (int)($input['custom_work_duration'] ?? 0);
$customBreak = (int)($input['custom_break_duration'] ?? 0);

if (!in_array($sessionType, ['work', 'break']) || $duration <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session data']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO sessions (user_id, task_id, session_type, duration, custom_work_duration, custom_break_duration)
        VALUES (:user_id, :task_id, :session_type, :duration, :custom_work_duration, :custom_break_duration)
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':task_id' => $taskId,
        ':session_type' => $sessionType,
        ':duration' => $duration,
        ':custom_work_duration' => $customWork,
        ':custom_break_duration' => $customBreak
    ]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database insert failed']);
}
