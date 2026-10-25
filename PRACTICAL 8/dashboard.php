<?php
require_once "db.php";

$studentCount = 0;
$eventCount = 0;
$studentsList = [];

if ($mysqliConnected) {
    $res1 = $conn->query("SELECT COUNT(*) AS total FROM students");
    if ($res1) {
        $studentCount = $res1->fetch_assoc()["total"];
    }

    $res2 = $conn->query("SELECT COUNT(*) AS total FROM events");
    if ($res2) {
        $eventCount = $res2->fetch_assoc()["total"];
    }

    $res3 = $conn->query("SELECT name, roll_no, branch, semester, attendance_pct FROM students ORDER BY id ASC");
    if ($res3) {
        while ($row = $res3->fetch_assoc()) {
            $studentsList[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Dashboard - Practical 8</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Student Dashboard (Database)</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <nav class="page-nav" id="mainNav">
            <a href="index.html">Home</a>
            <a href="dashboard.html" class="active">Dashboard</a>
            <a href="about.html">About</a>
            <a href="events.html">Events</a>
            <a href="faq.html">FAQ</a>
            <a href="contact.html">Contact</a>
            <a href="index.html">Logout</a>
        </nav>
    </header>

    <div class="card">
        <h2>MySQL Database Status</h2>
        <p><strong>MySQLi Status:</strong> <?php echo htmlspecialchars($mysqliStatus); ?></p>
        <p><strong>PDO Status:</strong> <?php echo htmlspecialchars($pdoStatus); ?></p>
    </div>

    <div class="info-grid">
        <div class="card">
            <h3>Registered Students</h3>
            <p style="font-size: 1.5rem; font-weight: bold; color: #356c9a;"><?php echo $studentCount; ?></p>
            <p>Records stored in <code>students</code> table.</p>
        </div>
        <div class="card">
            <h3>Active Events</h3>
            <p style="font-size: 1.5rem; font-weight: bold; color: #356c9a;"><?php echo $eventCount; ?></p>
            <p>Records stored in <code>events</code> table.</p>
        </div>
        <div class="card">
            <h3>Database Schema</h3>
            <p style="font-size: 1.5rem; font-weight: bold; color: #356c9a;">4 Tables</p>
            <p>users, students, events, registrations.</p>
        </div>
    </div>

    <div class="card">
        <h2>Live Student Database Records (MySQL Query)</h2>
        <?php if (!empty($studentsList)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studentsList as $st): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($st["name"]); ?></td>
                            <td><?php echo htmlspecialchars($st["roll_no"]); ?></td>
                            <td><?php echo htmlspecialchars($st["branch"]); ?></td>
                            <td>Semester <?php echo htmlspecialchars($st["semester"]); ?></td>
                            <td><?php echo htmlspecialchars($st["attendance_pct"]); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No student records detected. Import <code>schema.sql</code> into MySQL.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-links">
        <a href="profile.html">Student Profile</a>
        <a href="attendance.html">Attendance Record</a>
        <a href="timetable.html">Weekly Timetable</a>
        <a href="assignement.html">Assignments</a>
        <a href="result.html">Semester Results</a>
        <a href="notice.html">Notice Board</a>
        <a href="events.html">Campus Events</a>
        <a href="faq.html">Student FAQ</a>
        <a href="contact.html">Contact Helpdesk</a>
        <a href="index.html">Logout Portal</a>
    </div>

    <hr>
    <p align="center">&copy; 2026 StudentHub Portal - CHARUSAT FTE</p>

    <script src="script.js"></script>
</body>
</html>
