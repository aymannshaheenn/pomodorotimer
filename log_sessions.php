<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['task_name'], $data['type'], $data['duration_minutes'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$task_name = $data['task_name'];
$type = $data['type'];
$duration = $data['duration_minutes'];
$custom_work = isset($data['custom_work']) ? $data['custom_work'] : null;
$custom_break = isset($data['custom_break']) ? $data['custom_break'] : null;

$stmt = $conn->prepare("INSERT INTO sessions (user_id, task_name, type, duration_minutes, custom_work, custom_break) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issiii", $user_id, $task_name, $type, $duration, $custom_work, $custom_break);

if ($stmt->execute()) {
    echo json_encode(["message" => "Session logged successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database error"]);
}
?>
