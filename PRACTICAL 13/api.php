<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "db.php";

$action = $_GET["action"] ?? "list";

if ($_SERVER["REQUEST_METHOD"] === "GET" && $action === "list") {
    $result = $conn->query("SELECT * FROM events ORDER BY id DESC");
    $events = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    echo json_encode(["success" => true, "data" => $events]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "add") {
    $inputData = json_decode(file_get_contents("php://input"), true);
    if (!$inputData) {
        $inputData = $_POST;
    }

    $title = trim($inputData["title"] ?? "");
    $description = trim($inputData["description"] ?? "");
    $eventDate = trim($inputData["event_date"] ?? "");
    $venue = trim($inputData["venue"] ?? "");
    $category = trim($inputData["category"] ?? "");

    if (empty($title) || empty($description) || empty($eventDate) || empty($venue) || empty($category)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, venue, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $eventDate, $venue, $category);

    if ($stmt->execute()) {
        $newId = $stmt->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "Event created successfully.",
            "data" => [
                "id" => $newId,
                "title" => $title,
                "description" => $description,
                "event_date" => $eventDate,
                "venue" => $venue,
                "category" => $category
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database insertion error: " . $conn->error]);
    }
    $stmt->close();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "delete") {
    $inputData = json_decode(file_get_contents("php://input"), true);
    if (!$inputData) {
        $inputData = $_POST;
    }

    $id = intval($inputData["id"] ?? $_GET["id"] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Valid ID is required."]);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Event deleted successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to delete: " . $conn->error]);
    }
    $stmt->close();
    exit();
}

echo json_encode(["success" => false, "message" => "Invalid endpoint action."]);
exit();
?>
