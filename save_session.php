<?php
header('Content-Type: application/json');

try {
    session_start();

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        throw new Exception("Invalid JSON input.");
    }

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        throw new Exception("User not logged in.");
    }

    $task_id = $data['task_id'] ?? null;  
    $session_type = $data['session_type'] ?? null;
    $duration = $data['duration'] ?? null;
    $custom_work = $data['custom_work'] ?? null;
    $custom_break = $data['custom_break'] ?? null;

    if (!$session_type || !$duration) {
        throw new Exception("Missing required session data.");
    }

    $pdo = new PDO("mysql:host=localhost;dbname=pomodoro_task_manager", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        INSERT INTO sessions 
            (user_id, task_id, session_type, duration, custom_work_duration, custom_break_duration, timestamp) 
        VALUES 
            (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $task_id, $session_type, $duration, $custom_work, $custom_break]);

    $session_id = $pdo->lastInsertId();
    if (!$session_id) {
        throw new Exception("Failed to get last inserted session ID.");
    }

    $task_name = 'Untitled';
    if ($task_id) {
        $taskStmt = $pdo->prepare("SELECT title FROM tasks WHERE task_id = ?");
        $taskStmt->execute([$task_id]);
        $task = $taskStmt->fetch(PDO::FETCH_ASSOC);
        if ($task) {
            $task_name = $task['title'];
        }
    }

    $history = $pdo->prepare("
        INSERT INTO history 
            (user_id, session_id, task_id, task_name, session_type, duration, timestamp)
        VALUES 
            (:user_id, :session_id, :task_id, :task_name, :session_type, :duration, NOW())
    ");

    $history->execute([
        'user_id' => $user_id,
        'session_id' => $session_id,
        'task_id' => $task_id,      
        'task_name' => $task_name,
        'session_type' => $session_type,
        'duration' => $duration
    ]);

    if ($session_type === "work") {
        $xp_stmt = $pdo->prepare("UPDATE achievements SET xp = xp + 10 WHERE user_id = ?");
        $xp_stmt->execute([$user_id]);
    }

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}
?>
