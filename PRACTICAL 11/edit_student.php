<?php
require_once "db.php";

$message = "";
$messageType = "";
$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: student_crud.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $rollNo = trim($_POST["roll_no"] ?? "");
    $branch = trim($_POST["branch"] ?? "");
    $semester = intval($_POST["semester"] ?? 1);
    $phone = trim($_POST["phone"] ?? "");

    if (empty($name) || empty($email) || empty($rollNo) || empty($branch) || empty($phone)) {
        $message = "All fields are required.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, roll_no = ?, branch = ?, semester = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssssisi", $name, $email, $rollNo, $branch, $semester, $phone, $id);
        if ($stmt->execute()) {
            $message = "Student record updated successfully.";
            $messageType = "success";
        } else {
            $message = "Update failed: " . $conn->error;
            $messageType = "danger";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Edit Student Record</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <nav class="page-nav" id="mainNav">
            <a href="index.html">Home</a>
            <a href="dashboard.html">Dashboard</a>
            <a href="student_crud.php">Student CRUD</a>
            <a href="contact.html">Contact</a>
        </nav>
    </header>

    <?php if (!empty($message)): ?>
        <div class="card" style="font-weight: bold; color: <?php echo $messageType === 'success' ? '#166534' : '#991b1b'; ?>; background: <?php echo $messageType === 'success' ? '#f0fdf4' : '#fef2f2'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Update Student Information (UPDATE)</h2>
        <form method="POST" action="edit_student.php?id=<?php echo $id; ?>">
            <div>
                <label><strong>Full Name:</strong></label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>

            <div>
                <label><strong>Roll Number:</strong></label>
                <input type="text" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>" required>
            </div>

            <div>
                <label><strong>Email:</strong></label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>

            <div>
                <label><strong>Branch:</strong></label>
                <select name="branch" required>
                    <option value="Computer Engineering" <?php if ($student['branch'] === 'Computer Engineering') echo 'selected'; ?>>Computer Engineering</option>
                    <option value="Information Technology" <?php if ($student['branch'] === 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                    <option value="Electronics & Communication" <?php if ($student['branch'] === 'Electronics & Communication') echo 'selected'; ?>>Electronics & Communication</option>
                    <option value="Mechanical Engineering" <?php if ($student['branch'] === 'Mechanical Engineering') echo 'selected'; ?>>Mechanical Engineering</option>
                </select>
            </div>

            <div>
                <label><strong>Semester:</strong></label>
                <select name="semester" required>
                    <option value="1" <?php if ($student['semester'] == 1) echo 'selected'; ?>>Semester 1</option>
                    <option value="2" <?php if ($student['semester'] == 2) echo 'selected'; ?>>Semester 2</option>
                    <option value="3" <?php if ($student['semester'] == 3) echo 'selected'; ?>>Semester 3</option>
                    <option value="4" <?php if ($student['semester'] == 4) echo 'selected'; ?>>Semester 4</option>
                </select>
            </div>

            <div>
                <label><strong>Phone:</strong></label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
            </div>

            <div align="center">
                <input type="submit" value="Update Record">
                <a href="student_crud.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
