<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT task_name, session_type, duration, timestamp 
        FROM history 
        WHERE user_id = :user_id 
        ORDER BY timestamp DESC
    ");
    $stmt->execute(['user_id' => $user_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log('Fetched history rows: ' . count($history));
    error_log(print_r($history, true));

    echo json_encode($history);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
