<?php
require_once "auth.php";

$error = "";

if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                logAuditAction($conn, "USER_LOGIN_SUCCESS");

                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
                logAuditAction($conn, "FAILED_LOGIN_BAD_PASSWORD");
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Practical 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>StudentHub Portal</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <?php echo renderDynamicMenu("login"); ?>
    </header>

    <div class="card" style="background: #f1f5f9; border-left: 4px solid #356c9a;">
        <strong>Test Credentials:</strong><br>
        Admin Account: <code>admin</code> / Password: <code>admin123</code><br>
        Student Account: <code>gehna_patel</code> / Password: <code>password123</code>
    </div>

    <?php if (!empty($error)): ?>
        <div class="card" style="color: #991b1b; background: #fef2f2; font-weight: bold;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Sign In (Role-Based Access)</h2>
        <form method="POST" action="login.php">
            <div>
                <label><strong>Username:</strong></label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>

            <div>
                <label><strong>Password:</strong></label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>

            <div align="center">
                <input type="submit" value="Sign In">
                <input type="reset" value="Clear">
            </div>

            <hr>
            <p align="center">
                Don't have an account? <a href="register.html">Register Here</a>
            </p>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
