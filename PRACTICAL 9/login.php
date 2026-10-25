<?php
require_once "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameOrEmail = trim($_POST["login_id"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($usernameOrEmail) || empty($password)) {
        $message = "Please enter both username/email and password.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $message = "Password verified successfully! Welcome, " . htmlspecialchars($user["username"]) . " (" . htmlspecialchars($user["role"]) . ").";
                $messageType = "success";
            } else {
                $message = "Invalid password. password_verify() returned false.";
                $messageType = "danger";
            }
        } else {
            $message = "No account found with this username or email.";
            $messageType = "danger";
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
    <title>Practical 9 - Verify Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <h1>StudentHub Portal</h1>
        <p>Practical 9: Login Verification with password_verify()</p>
    </header>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Account Login Test</h2>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="login_id">Username or Email</label>
                <input type="text" id="login_id" name="login_id" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn">Test Login & Verify Hash</button>

            <div class="form-footer">
                Need an account? <a href="index.php">Register Here</a>
            </div>
        </form>
    </div>

</body>
</html>
