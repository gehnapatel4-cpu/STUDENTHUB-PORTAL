<?php
require_once "db.php";

$message = "";
$messageType = "";

if (isset($_GET["delete_id"])) {
    $deleteId = intval($_GET["delete_id"]);

    $stmt = $conn->prepare("SELECT poster_image FROM events WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (!empty($row["poster_image"]) && file_exists($row["poster_image"])) {
            unlink($row["poster_image"]);
        }
    }
    $stmt->close();

    $del = $conn->prepare("DELETE FROM events WHERE id = ?");
    $del->bind_param("i", $deleteId);
    if ($del->execute()) {
        $message = "Event and associated poster deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Error deleting event: " . $conn->error;
        $messageType = "danger";
    }
    $del->close();
}

if (isset($_GET["toggle_status"])) {
    $toggleId = intval($_GET["toggle_status"]);
    $curr = $_GET["curr"] === "open" ? "closed" : "open";
    $stmt = $conn->prepare("UPDATE events SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $curr, $toggleId);
    $stmt->execute();
    $stmt->close();
    header("Location: event_crud.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_event"])) {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");

    if (empty($title) || empty($description) || empty($eventDate) || empty($venue) || empty($category)) {
        $message = "All event text fields are required.";
        $messageType = "danger";
    } else {
        $posterPath = "";
        $uploadOk = true;

        if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES["poster"]["tmp_name"];
            $fileName = basename($_FILES["poster"]["name"]);
            $fileSize = $_FILES["poster"]["size"];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowed = ["jpg", "jpeg", "png", "webp"];

            if (!in_array($ext, $allowed)) {
                $message = "Invalid file type. Only JPG, JPEG, PNG, and WEBP files are allowed.";
                $messageType = "danger";
                $uploadOk = false;
            } elseif ($fileSize > (2 * 1024 * 1024)) {
                $message = "File size exceeds 2MB limit. Please upload a smaller image.";
                $messageType = "danger";
                $uploadOk = false;
            } else {
                $uniqueName = "poster_" . time() . "_" . rand(1000, 9999) . "." . $ext;
                $targetFile = "uploads/" . $uniqueName;

                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $posterPath = $targetFile;
                } else {
                    $message = "Failed to upload image file.";
                    $messageType = "danger";
                    $uploadOk = false;
                }
            }
        }

        if ($uploadOk) {
            $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, venue, category, poster_image, status) VALUES (?, ?, ?, ?, ?, ?, 'open')");
            $stmt->bind_param("ssssss", $title, $description, $eventDate, $venue, $category, $posterPath);
            if ($stmt->execute()) {
                $message = "Event added successfully with validated poster upload.";
                $messageType = "success";
            } else {
                $message = "Database error: " . $conn->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
    }
}

$events = [];
$res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management CRUD - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Event Management (CRUD &amp; Poster Upload)</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <nav class="page-nav" id="mainNav">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="events.html">Events</a>
            <a href="faq.html">FAQ</a>
            <a href="dashboard.html">Dashboard</a>
            <a href="event_crud.php" class="active">Manage Events</a>
            <a href="contact.html">Contact</a>
        </nav>
    </header>

    <?php if (!empty($message)): ?>
        <div class="card" style="font-weight: bold; color: <?php echo $messageType === 'success' ? '#166534' : '#991b1b'; ?>; background: <?php echo $messageType === 'success' ? '#f0fdf4' : '#fef2f2'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Add Campus Event with Poster Upload</h2>
        <form method="POST" action="event_crud.php" enctype="multipart/form-data">
            <input type="hidden" name="add_event" value="1">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px;">
                <div>
                    <label><strong>Event Title:</strong></label>
                    <input type="text" name="title" placeholder="Event title" required>
                </div>
                <div>
                    <label><strong>Category:</strong></label>
                    <select name="category" required>
                        <option value="Technical">Technical</option>
                        <option value="Sports">Sports</option>
                        <option value="Cultural">Cultural</option>
                        <option value="Academic">Academic</option>
                    </select>
                </div>
                <div>
                    <label><strong>Event Date:</strong></label>
                    <input type="date" name="event_date" required>
                </div>
                <div>
                    <label><strong>Venue:</strong></label>
                    <input type="text" name="venue" placeholder="Hall / Ground" required>
                </div>
            </div>

            <div style="margin-top: 10px;">
                <label><strong>Description:</strong></label>
                <textarea name="description" rows="3" placeholder="Brief event description" required></textarea>
            </div>

            <div style="margin-top: 10px;">
                <label><strong>Event Poster Image (Max 2MB, JPG/PNG/WEBP):</strong></label>
                <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
            </div>

            <div align="center" style="margin-top: 14px;">
                <input type="submit" value="Upload &amp; Create Event">
                <input type="reset" value="Clear">
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Campus Events Database Records</h2>
        <?php if (empty($events)): ?>
            <p>No events found. Add your first event using the form above.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title &amp; Category</th>
                        <th>Date &amp; Venue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $ev): ?>
                        <tr>
                            <td style="width: 80px; text-align: center;">
                                <?php if (!empty($ev["poster_image"]) && file_exists($ev["poster_image"])): ?>
                                    <img src="<?php echo htmlspecialchars($ev["poster_image"]); ?>" alt="Poster" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #dbe4ec;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; background: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #64748b;">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($ev["title"]); ?></strong><br>
                                <span style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($ev["category"]); ?></span>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($ev["event_date"]); ?><br>
                                <span style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($ev["venue"]); ?></span>
                            </td>
                            <td>
                                <a href="event_crud.php?toggle_status=<?php echo $ev["id"]; ?>&curr=<?php echo $ev["status"]; ?>" class="btn" style="padding: 4px 10px; font-size: 0.8rem; background: <?php echo $ev["status"] === 'open' ? '#16a34a' : '#64748b'; ?>;">
                                    <?php echo strtoupper($ev["status"]); ?>
                                </a>
                            </td>
                            <td>
                                <a href="event_crud.php?delete_id=<?php echo $ev["id"]; ?>" onclick="return confirm('Delete this event and its poster image?');" class="btn" style="padding: 4px 10px; font-size: 0.8rem; background: #dc2626;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <hr>
    <p align="center">
        <a href="index.html" class="btn">Back to Home</a>
        <a href="dashboard.html" class="btn btn-secondary">Go to Dashboard</a>
    </p>

    <script src="script.js"></script>
</body>
</html>
