<?php
$successMessage = "";
$errors = [];

$name = "";
$email = "";
$phone = "";
$branch = "";
$semester = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $branch = trim($_POST["branch"] ?? "");
    $semester = trim($_POST["semester"] ?? "");

    if (empty($name)) {
        $errors[] = "Full Name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]{3,50}$/", $name)) {
        $errors[] = "Full Name must contain letters and spaces only (minimum 3 characters).";
    }

    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match("/^[6-9]\d{9}$/", $phone)) {
        $errors[] = "Phone number must be a 10-digit number starting with 6, 7, 8, or 9.";
    }

    if (empty($branch)) {
        $errors[] = "Please select a branch.";
    }

    if (empty($semester) || !in_array($semester, ["1", "2", "3", "4", "5", "6", "7", "8"])) {
        $errors[] = "Please select a valid semester.";
    }

    if (empty($errors)) {
        $createdAt = date("Y-m-d H:i:s");
        $newStudent = [
            "name" => htmlspecialchars($name),
            "email" => htmlspecialchars($email),
            "phone" => htmlspecialchars($phone),
            "branch" => htmlspecialchars($branch),
            "semester" => htmlspecialchars($semester),
            "created_at" => $createdAt
        ];

        $jsonFile = "students.json";
        $currentJson = file_exists($jsonFile) ? file_get_contents($jsonFile) : "[]";
        $studentList = json_decode($currentJson, true);
        if (!is_array($studentList)) {
            $studentList = [];
        }
        $studentList[] = $newStudent;
        file_put_contents($jsonFile, json_encode($studentList, JSON_PRETTY_PRINT));

        $csvFile = "students.csv";
        $csvLine = [$newStudent["name"], $newStudent["email"], $newStudent["phone"], $newStudent["branch"], $newStudent["semester"], $createdAt];
        $fileHandle = fopen($csvFile, "a");
        if ($fileHandle !== false) {
            fputcsv($fileHandle, $csvLine);
            fclose($fileHandle);
        }

        $successMessage = "Registration for " . htmlspecialchars($name) . " stored successfully in JSON and CSV!";
        $name = "";
        $email = "";
        $phone = "";
        $branch = "";
        $semester = "";
    }
}

$storedStudents = [];
if (file_exists("students.json")) {
    $data = file_get_contents("students.json");
    $storedStudents = json_decode($data, true);
    if (!is_array($storedStudents)) {
        $storedStudents = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Practical 7</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Student Registration (PHP)</h1>
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
            <a href="login.html">Login</a>
            <a href="register.php" class="active">Register</a>
            <a href="contact.html">Contact</a>
        </nav>
    </header>

    <?php if (!empty($successMessage)): ?>
        <div class="card" style="border-color: #16a34a; background: #f0fdf4; color: #166534; font-weight: bold;">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="card" style="border-color: #dc2626; background: #fef2f2; color: #991b1b;">
            <strong>Please fix the following validation errors:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Register Student (Server-Side Validation)</h2>
        <form method="POST" action="register.php">
            <div>
                <label for="name"><strong>Full Name:</strong></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter full name" required>
            </div>

            <div>
                <label for="email"><strong>Email Address:</strong></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="student@domain.com" required>
            </div>

            <div>
                <label for="phone"><strong>Phone Number:</strong></label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="10-digit mobile number" required>
            </div>

            <div>
                <label for="branch"><strong>Branch / Department:</strong></label>
                <select id="branch" name="branch" required>
                    <option value="">-- Select Branch --</option>
                    <option value="CE" <?php if ($branch === "CE") echo "selected"; ?>>Computer Engineering (CE)</option>
                    <option value="IT" <?php if ($branch === "IT") echo "selected"; ?>>Information Technology (IT)</option>
                    <option value="EC" <?php if ($branch === "EC") echo "selected"; ?>>Electronics & Communication (EC)</option>
                    <option value="ME" <?php if ($branch === "ME") echo "selected"; ?>>Mechanical Engineering (ME)</option>
                </select>
            </div>

            <div>
                <label for="semester"><strong>Semester:</strong></label>
                <select id="semester" name="semester" required>
                    <option value="">-- Select Semester --</option>
                    <option value="1" <?php if ($semester === "1") echo "selected"; ?>>Semester 1</option>
                    <option value="2" <?php if ($semester === "2") echo "selected"; ?>>Semester 2</option>
                    <option value="3" <?php if ($semester === "3") echo "selected"; ?>>Semester 3</option>
                    <option value="4" <?php if ($semester === "4") echo "selected"; ?>>Semester 4</option>
                </select>
            </div>

            <div align="center">
                <input type="submit" value="Submit & Save Record">
                <input type="reset" value="Clear">
            </div>

            <hr>
            <p align="center">
                Already registered? <a href="login.html">Login Here</a>
            </p>
        </form>
    </div>

    <div class="card">
        <h2>Stored Student Records (JSON &amp; CSV)</h2>
        <?php if (empty($storedStudents)): ?>
            <p>No records stored yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Recorded At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($storedStudents as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($item["name"]); ?></td>
                            <td><?php echo htmlspecialchars($item["email"]); ?></td>
                            <td><?php echo htmlspecialchars($item["phone"]); ?></td>
                            <td><?php echo htmlspecialchars($item["branch"]); ?></td>
                            <td>Sem <?php echo htmlspecialchars($item["semester"]); ?></td>
                            <td><?php echo htmlspecialchars($item["created_at"]); ?></td>
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
