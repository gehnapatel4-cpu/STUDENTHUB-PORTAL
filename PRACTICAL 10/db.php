<?php
$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}
?>
