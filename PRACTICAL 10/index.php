<?php
require_once "auth.php";
require_once "db.php";

$error = "";
$info = "";

if (isset($_GET["timeout"])) {
    $info = "Your session has timed out due to 15 minutes of inactivity. Please log in again.";
} elseif (isset($_GET["logged_out"])) {
    $info = "You have been logged out securely.";
} elseif (isset($_GET["unauthorized"])) {
    $error = "Please log in to access the dashboard.";
}

if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        header("Location: dashboard.php");
        exit();
    }
}

$rememberedUser = $_COOKIE["remember_username"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]);

    if (empty($username) || empty($password)) {
        $error = "Both username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                if ($remember) {
                    setcookie("remember_username", $user["username"], time() + (30 * 24 * 60 * 60), "/", "", false, true);
                } else {
                    setcookie("remember_username", "", time() - 3600, "/");
                }

                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password. Authentication failed.";
            }
        } else {
            $error = "No account found matching this username or email.";
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
    <title>Practical 10 - Secure Session Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <h1>StudentHub Portal</h1>
        <span>Secure Authentication</span>
    </header>

    <?php if (!empty($info)): ?>
        <div class="alert alert-warning">
            <?php echo htmlspecialchars($info); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="demo-credentials">
        <strong>Default Test Credentials:</strong><br>
        Student: <code>gehna_patel</code> | Password: <code>password123</code><br>
        Admin: <code>admin</code> | Password: <code>admin123</code>
    </div>

    <div class="card">
        <h2>Secure User Login</h2>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($rememberedUser); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" <?php if (!empty($rememberedUser)) echo "checked"; ?>> Remember my username on this device
                </label>
            </div>

            <button type="submit" class="btn btn-block">Sign In with Session</button>
        </form>
    </div>

</body>
</html>
