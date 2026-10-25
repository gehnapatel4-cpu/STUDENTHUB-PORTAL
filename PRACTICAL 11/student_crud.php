<?php
require_once "db.php";

$message = "";
$messageType = "";

if (isset($_GET["delete_id"])) {
    $deleteId = intval($_GET["delete_id"]);
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $message = "Student record deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Failed to delete student: " . $conn->error;
        $messageType = "danger";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_student"])) {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $rollNo = trim($_POST["roll_no"] ?? "");
    $branch = trim($_POST["branch"] ?? "");
    $semester = intval($_POST["semester"] ?? 1);
    $phone = trim($_POST["phone"] ?? "");

    if (empty($name) || empty($email) || empty($rollNo) || empty($branch) || empty($phone)) {
        $message = "All fields are required to register a student.";
        $messageType = "danger";
    } else {
        $check = $conn->prepare("SELECT id FROM students WHERE roll_no = ?");
        $check->bind_param("s", $rollNo);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $message = "A student with Roll No '$rollNo' already exists.";
            $messageType = "danger";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (name, email, roll_no, branch, semester, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssis", $name, $email, $rollNo, $branch, $semester, $phone);
            if ($stmt->execute()) {
                $message = "Student record added successfully.";
                $messageType = "success";
            } else {
                $message = "Error inserting record: " . $conn->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
        $check->close();
    }
}

$search = trim($_GET["search"] ?? "");
$filterBranch = trim($_GET["branch"] ?? "");

$query = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR roll_no LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

if (!empty($filterBranch)) {
    $query .= " AND branch = ?";
    $params[] = $filterBranch;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student CRUD Management - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Student Management (CRUD)</h1>
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
            <a href="student_crud.php" class="active">Student CRUD</a>
            <a href="contact.html">Contact</a>
        </nav>
    </header>

    <?php if (!empty($message)): ?>
        <div class="card" style="font-weight: bold; color: <?php echo $messageType === 'success' ? '#166534' : '#991b1b'; ?>; background: <?php echo $messageType === 'success' ? '#f0fdf4' : '#fef2f2'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Add New Student Record (INSERT)</h2>
        <form method="POST" action="student_crud.php">
            <input type="hidden" name="add_student" value="1">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                <div>
                    <label><strong>Full Name:</strong></label>
                    <input type="text" name="name" placeholder="Student name" required>
                </div>
                <div>
                    <label><strong>Roll Number:</strong></label>
                    <input type="text" name="roll_no" placeholder="e.g. 23CE099" required>
                </div>
                <div>
                    <label><strong>Email:</strong></label>
                    <input type="email" name="email" placeholder="student@charusat.edu.in" required>
                </div>
                <div>
                    <label><strong>Branch / Department:</strong></label>
                    <select name="branch" required>
                        <option value="">-- Select Branch --</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Electronics & Communication">Electronics & Communication</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                    </select>
                </div>
                <div>
                    <label><strong>Semester:</strong></label>
                    <select name="semester" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3" selected>Semester 3</option>
                        <option value="4">Semester 4</option>
                    </select>
                </div>
                <div>
                    <label><strong>Phone:</strong></label>
                    <input type="text" name="phone" placeholder="10-digit phone" required>
                </div>
            </div>

            <div align="center" style="margin-top: 14px;">
                <input type="submit" value="Save Student Record">
                <input type="reset" value="Clear">
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Student Directory (Search &amp; Filter)</h2>
        <form method="GET" action="student_crud.php" style="width: 100%; max-width: 100%; border: none; padding: 0; box-shadow: none;">
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, roll no, or email..." style="flex: 2; min-width: 200px;">
                <select name="branch" style="flex: 1; min-width: 160px;">
                    <option value="">All Branches</option>
                    <option value="Computer Engineering" <?php if ($filterBranch === "Computer Engineering") echo "selected"; ?>>Computer Engineering</option>
                    <option value="Information Technology" <?php if ($filterBranch === "Information Technology") echo "selected"; ?>>Information Technology</option>
                    <option value="Electronics & Communication" <?php if ($filterBranch === "Electronics & Communication") echo "selected"; ?>>Electronics & Communication</option>
                    <option value="Mechanical Engineering" <?php if ($filterBranch === "Mechanical Engineering") echo "selected"; ?>>Mechanical Engineering</option>
                </select>
                <input type="submit" value="Filter" style="width: auto;">
                <a href="student_crud.php" class="btn btn-secondary" style="line-height: 20px;">Reset</a>
            </div>
        </form>

        <?php if (empty($students)): ?>
            <p>No student records found matching the query.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $st): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($st["roll_no"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($st["name"]); ?></td>
                            <td><?php echo htmlspecialchars($st["email"]); ?></td>
                            <td><?php echo htmlspecialchars($st["branch"]); ?></td>
                            <td>Sem <?php echo htmlspecialchars($st["semester"]); ?></td>
                            <td><?php echo htmlspecialchars($st["phone"]); ?></td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $st["id"]; ?>" class="btn" style="padding: 4px 8px; font-size: 0.85rem;">Edit</a>
                                <a href="student_crud.php?delete_id=<?php echo $st["id"]; ?>" onclick="return confirm('Are you sure you want to delete this student record?');" class="btn" style="padding: 4px 8px; font-size: 0.85rem; background: #dc2626;">Delete</a>
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
