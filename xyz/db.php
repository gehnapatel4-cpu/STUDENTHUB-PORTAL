<?php
$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    $mysqliStatus = "Connection failed: " . $conn->connect_error;
    $mysqliConnected = false;
} else {
    $mysqliStatus = "Connected successfully to MySQL using MySQLi";
    $mysqliConnected = true;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdoStatus = "Connected successfully to MySQL using PDO";
    $pdoConnected = true;
} catch (PDOException $e) {
    $pdo = null;
    $pdoStatus = "PDO Connection failed: " . $e->getMessage();
    $pdoConnected = false;
}
?>
