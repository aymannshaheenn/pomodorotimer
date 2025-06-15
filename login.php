<?php
session_start(); 
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing username or password"]);
    exit;
}

$username = trim($data['username']);
$password = $data['password'];

$stmt = $pdo->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $username;

    echo json_encode([
        "message" => "Login successful",
        "user_id" => $user['user_id'],
        "username" => $username
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid username or password"]);
}
?>
