<?php
require_once "auth.php";
requireLogin();

logAuditAction($conn, "VIEWED_STUDENT_DASHBOARD");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Practical 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Student Dashboard</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <?php echo renderDynamicMenu("dashboard"); ?>
    </header>

    <?php if (isset($_GET["msg"]) && $_GET["msg"] === "access_denied"): ?>
        <div class="card" style="color: #991b1b; background: #fef2f2; font-weight: bold;">
            Access Denied: Only administrators can access the Admin Dashboard.
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
        <p>Your current active role is: <strong><?php echo htmlspecialchars(strtoupper($_SESSION["role"])); ?></strong>.</p>
        <p>The navigation bar above automatically updates depending on your logged-in role.</p>
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
        <a href="logout.php">Logout</a>
    </div>

    <hr>
    <p align="center">&copy; 2026 StudentHub Portal</p>

    <script src="script.js"></script>
</body>
</html>
