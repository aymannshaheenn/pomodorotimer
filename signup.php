<?php
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

if ($username === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["error" => "Username and password cannot be empty"]);
    exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(["error" => "Username already exists"]);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
$stmt->execute([$username, $passwordHash]);

echo json_encode(["message" => "Signup successful"]);
?>
