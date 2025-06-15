<?php
$servername = "localhost"; 
$username = "root";      
$password = "";            
$database = "pomodoro_task_manager";   

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connection successful to database: " . $database;

$conn->close();
?>
