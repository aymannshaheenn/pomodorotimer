<?php
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$task_id = $data['task_id'] ?? null;
$sessions = $data['sessions'] ?? null;

if (!$task_id || $sessions === null) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE tasks SET sessions = ? WHERE task_id = ?");
    $stmt->execute([$sessions, $task_id]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
