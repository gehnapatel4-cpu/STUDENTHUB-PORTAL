<?php
require_once "auth.php";
require_once "db.php";
requireRole("admin");

$usersList = [];
$res = $conn->query("SELECT id, username, email, role, created_at FROM users");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $usersList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <h1>StudentHub Portal</h1>
        <div class="user-nav">
            <span>Admin: <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <span class="badge badge-admin">ADMINISTRATOR</span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </header>

    <div class="card">
        <h2>Administrator Control Panel</h2>
        <p>You have full access to manage user accounts, permissions, and session audits.</p>
        <p><strong>Session ID:</strong> <code><?php echo session_id(); ?></code></p>
    </div>

    <div class="card">
        <h2>System Users (Role-Based Access Overview)</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #24344d; color: #ffffff; text-align: left;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Username</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Role</th>
                    <th style="padding: 10px;">Created Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersList as $u): ?>
                    <tr style="border-bottom: 1px solid #dbe4ec;">
                        <td style="padding: 10px;"><?php echo htmlspecialchars($u["id"]); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($u["username"]); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($u["email"]); ?></td>
                        <td style="padding: 10px;">
                            <span class="badge <?php echo $u["role"] === 'admin' ? 'badge-admin' : 'badge-student'; ?>">
                                <?php echo htmlspecialchars(strtoupper($u["role"])); ?>
                            </span>
                        </td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($u["created_at"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
