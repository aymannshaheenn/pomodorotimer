<?php
ob_clean();

header('Content-Type: application/json');
error_reporting(0);
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

try {
    if ($method === 'GET') {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tasks);
    
    } elseif ($method === 'POST') {
        $title = $input['title'] ?? '';
        $priority = $input['priority'] ?? 'Low';
        $sessions = $input['sessions'] ?? 1;
        $notes = $input['notes'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, priority, sessions, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $priority, $sessions, $notes]);
        echo json_encode(['message' => 'Task added']);

    } elseif ($method === 'PUT') {
        $task_id = $input['task_id'] ?? 0;
        $title = $input['title'] ?? '';
        $priority = $input['priority'] ?? 'Low';
        $sessions = $input['sessions'] ?? 1;
        $notes = $input['notes'] ?? '';

        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, priority = ?, sessions = ?, notes = ? WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$title, $priority, $sessions, $notes, $task_id, $user_id]);
        echo json_encode(['message' => 'Task updated']);

    } elseif ($method === 'DELETE') {
        parse_str(file_get_contents("php://input"), $data);
        $task_id = $data['task_id'] ?? 0;

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$task_id, $user_id]);
        echo json_encode(['message' => 'Task deleted']);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query error']);
}
?>
