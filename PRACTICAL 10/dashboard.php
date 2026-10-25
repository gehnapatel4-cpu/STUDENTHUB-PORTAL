<?php
require_once "auth.php";
requireLogin();

$forbidden = isset($_GET["forbidden"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <h1>StudentHub Portal</h1>
        <div class="user-nav">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <span class="badge badge-student"><?php echo htmlspecialchars(strtoupper($_SESSION["role"])); ?></span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </header>

    <?php if ($forbidden): ?>
        <div class="alert alert-danger">
            Access Denied: You do not have permission to access the Administrator Dashboard.
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Student Dashboard</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION["username"]); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
        <p><strong>Session ID:</strong> <code><?php echo session_id(); ?></code></p>
        <p><strong>Session Timeout:</strong> 15 minutes of inactivity will automatically log out your account.</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-item">
            <h3>Attendance</h3>
            <p>90% Overall Attendance</p>
        </div>
        <div class="dashboard-item">
            <h3>Timetable</h3>
            <p>Monday - Saturday</p>
        </div>
        <div class="dashboard-item">
            <h3>Assignments</h3>
            <p>Practical 10 Completed</p>
        </div>
        <div class="dashboard-item">
            <h3>Results</h3>
            <p>Semester 2: CPI 8.92</p>
        </div>
    </div>

</body>
</html>
