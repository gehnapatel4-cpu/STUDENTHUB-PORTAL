<?php
$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Database connection error: " . $conn->connect_error]);
    exit();
}
?>
