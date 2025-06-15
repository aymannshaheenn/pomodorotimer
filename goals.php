<?php
ob_clean();
header('Content-Type: application/json');
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
        $stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    } elseif ($method === 'POST') {
        $goal_text = $input['goal_text'] ?? '';
        $stmt = $pdo->prepare("INSERT INTO goals (user_id, goal_text, is_achieved) VALUES (?, ?, 0)");
        $stmt->execute([$user_id, $goal_text]);
        echo json_encode(['message' => 'Goal added']);

    } elseif ($method === 'PUT') {
        $goal_id = $input['goal_id'] ?? 0;
        $is_achieved = $input['is_achieved'] ?? 0;
        if ($is_achieved) {
            $stmt = $pdo->prepare("UPDATE goals SET is_achieved = 1, achieved_at = CURRENT_TIMESTAMP WHERE goal_id = ? AND user_id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE goals SET is_achieved = 0, achieved_at = NULL WHERE goal_id = ? AND user_id = ?");
        }
        $stmt->execute([$goal_id, $user_id]);
        echo json_encode(['message' => 'Goal updated']);

    } elseif ($method === 'DELETE') {
        parse_str(file_get_contents("php://input"), $data);
        $goal_id = $data['goal_id'] ?? null;

        if ($goal_id === 'all_achieved') {
            $stmt = $pdo->prepare("DELETE FROM goals WHERE user_id = ? AND is_achieved = 1");
            $stmt->execute([$user_id]);
            echo json_encode(['message' => 'Achieved goals cleared']);
        } else {
            $stmt = $pdo->prepare("DELETE FROM goals WHERE goal_id = ? AND user_id = ?");
            $stmt->execute([$goal_id, $user_id]);
            echo json_encode(['message' => 'Goal deleted']);
        }

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
