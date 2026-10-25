<?php
require_once "db.php";

$success = "";
$errors = [];

$username = "";
$email = "";
$role = "student";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "student";

    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $errors[] = "Username must be 3-20 characters long and contain only letters, numbers, and underscores.";
    }

    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $checkStmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $existing = $checkResult->fetch_assoc();
            if ($existing["username"] === $username) {
                $errors[] = "The username '$username' is already taken.";
            }
            if ($existing["email"] === $email) {
                $errors[] = "The email address '$email' is already registered.";
            }
        }
        $checkStmt->close();
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertStmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

        if ($insertStmt->execute()) {
            $success = "User registered successfully! You can now log in.";
            $username = "";
            $email = "";
            $role = "student";
        } else {
            $errors[] = "Failed to insert user into database: " . $conn->error;
        }
        $insertStmt->close();
    }
}

$registeredUsers = [];
$res = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC LIMIT 10");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $registeredUsers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Secure User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <h1>StudentHub Portal</h1>
        <p>Practical 9: Secure Registration, Duplicate Check & Password Hashing</p>
    </header>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Registration failed:</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Create StudentHub Account</h2>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-type password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="student" <?php if ($role === "student") echo "selected"; ?>>Student</option>
                    <option value="faculty" <?php if ($role === "faculty") echo "selected"; ?>>Faculty</option>
                    <option value="admin" <?php if ($role === "admin") echo "selected"; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn">Register Securely</button>

            <div class="form-footer">
                Already registered? <a href="login.php">Proceed to Login</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Registered Users in Database</h2>
        <?php if (!empty($registeredUsers)): ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registeredUsers as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user["id"]); ?></td>
                            <td><?php echo htmlspecialchars($user["username"]); ?></td>
                            <td><?php echo htmlspecialchars($user["email"]); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($user["role"])); ?></td>
                            <td><?php echo htmlspecialchars($user["created_at"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No registered users found in the database table yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>
