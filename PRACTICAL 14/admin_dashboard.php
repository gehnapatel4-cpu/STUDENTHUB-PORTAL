<?php
require_once "auth.php";
requireAdmin();

logAuditAction($conn, "VIEWED_ADMIN_DASHBOARD");

$userCount = 0;
$studentCount = 0;
$eventCount = 0;
$regCount = 0;

$r1 = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($r1) $userCount = $r1->fetch_assoc()["total"];

$r2 = $conn->query("SELECT COUNT(*) AS total FROM students");
if ($r2) $studentCount = $r2->fetch_assoc()["total"];

$r3 = $conn->query("SELECT COUNT(*) AS total FROM events");
if ($r3) $eventCount = $r3->fetch_assoc()["total"];

$r4 = $conn->query("SELECT COUNT(*) AS total FROM registrations");
if ($r4) $regCount = $r4->fetch_assoc()["total"];

$recentRegs = [];
$qRegs = "SELECT r.id, s.name, s.roll_no, e.title, r.registered_at 
          FROM registrations r 
          JOIN students s ON r.student_id = s.id 
          JOIN events e ON r.event_id = e.id 
          ORDER BY r.id DESC LIMIT 5";
$resRegs = $conn->query($qRegs);
if ($resRegs) {
    while ($row = $resRegs->fetch_assoc()) {
        $recentRegs[] = $row;
    }
}

$logs = [];
$resLogs = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 8");
if ($resLogs) {
    while ($row = $resLogs->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Practical 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>Admin Control Panel (RBAC)</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <?php echo renderDynamicMenu("admin"); ?>
    </header>

    <div class="card">
        <h2>Administrator Overview</h2>
        <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong> | Role: <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-weight: bold;">ADMIN</span></p>
    </div>

    <h2>System Statistics (SQL Aggregations)</h2>
    <div class="info-grid">
        <div class="card" style="text-align: center;">
            <h3>Total Users</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #356c9a; margin: 0;"><?php echo $userCount; ?></p>
            <p style="font-size: 0.85rem; color: #64748b;">Active accounts</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3>Students</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #356c9a; margin: 0;"><?php echo $studentCount; ?></p>
            <p style="font-size: 0.85rem; color: #64748b;">Enrolled students</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3>Events</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #356c9a; margin: 0;"><?php echo $eventCount; ?></p>
            <p style="font-size: 0.85rem; color: #64748b;">Active campus events</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3>Registrations</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #356c9a; margin: 0;"><?php echo $regCount; ?></p>
            <p style="font-size: 0.85rem; color: #64748b;">Event sign-ups</p>
        </div>
    </div>

    <div class="card">
        <h2>Latest Event Registrations (SQL JOIN)</h2>
        <?php if (empty($recentRegs)): ?>
            <p>No recent registrations found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Event Title</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentRegs as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r["id"]); ?></td>
                            <td><?php echo htmlspecialchars($r["name"]); ?></td>
                            <td><?php echo htmlspecialchars($r["roll_no"]); ?></td>
                            <td><?php echo htmlspecialchars($r["title"]); ?></td>
                            <td><?php echo htmlspecialchars($r["registered_at"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Security Audit Logs (audit_logs Table)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($l["id"]); ?></td>
                        <td><strong><?php echo htmlspecialchars($l["username"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($l["role"]); ?></td>
                        <td><code><?php echo htmlspecialchars($l["action"]); ?></code></td>
                        <td><?php echo htmlspecialchars($l["ip_address"]); ?></td>
                        <td><?php echo htmlspecialchars($l["created_at"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <hr>
    <p align="center">
        <a href="logout.php" class="btn" style="background: #dc2626;">Logout Admin</a>
        <a href="index.html" class="btn btn-secondary">Portal Home</a>
    </p>

    <script src="script.js"></script>
</body>
</html>
