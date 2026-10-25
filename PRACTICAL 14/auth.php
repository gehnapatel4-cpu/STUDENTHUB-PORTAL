<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

function requireLogin() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php?msg=login_required");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION["role"] !== "admin") {
        header("Location: dashboard.php?msg=access_denied");
        exit();
    }
}

function logAuditAction($conn, $action) {
    $username = $_SESSION["username"] ?? "guest";
    $role = $_SESSION["role"] ?? "anonymous";
    $ip = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";

    $stmt = $conn->prepare("INSERT INTO audit_logs (username, role, action, ip_address) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $username, $role, $action, $ip);
        $stmt->execute();
        $stmt->close();
    }
}

function renderDynamicMenu($activePage = "") {
    $role = $_SESSION["role"] ?? "guest";
    $html = '<nav class="page-nav" id="mainNav">';
    $html .= '<a href="index.html"' . ($activePage === "index" ? ' class="active"' : '') . '>Home</a>';
    $html .= '<a href="about.html"' . ($activePage === "about" ? ' class="active"' : '') . '>About</a>';
    $html .= '<a href="events.html"' . ($activePage === "events" ? ' class="active"' : '') . '>Events</a>';

    if ($role === "admin") {
        $html .= '<a href="admin_dashboard.php"' . ($activePage === "admin" ? ' class="active"' : '') . '>Admin Dashboard</a>';
        $html .= '<a href="logout.php">Logout (' . htmlspecialchars($_SESSION["username"]) . ')</a>';
    } elseif ($role === "student") {
        $html .= '<a href="dashboard.php"' . ($activePage === "dashboard" ? ' class="active"' : '') . '>Student Dashboard</a>';
        $html .= '<a href="attendance.html">Attendance</a>';
        $html .= '<a href="timetable.html">Timetable</a>';
        $html .= '<a href="logout.php">Logout</a>';
    } else {
        $html .= '<a href="login.php"' . ($activePage === "login" ? ' class="active"' : '') . '>Login</a>';
        $html .= '<a href="register.html">Register</a>';
    }

    $html .= '<a href="contact.html">Contact</a>';
    $html .= '</nav>';
    return $html;
}
?>
