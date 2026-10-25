<?php
require_once "db.php";

$tableNames = [];
$studentsData = [];

if ($mysqliConnected) {
    $res = $conn->query("SHOW TABLES");
    if ($res) {
        while ($row = $res->fetch_array()) {
            $tableNames[] = $row[0];
        }
    }

    $stmt = $conn->query("SELECT name, roll_no, branch, semester, phone, attendance_pct FROM students LIMIT 5");
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $studentsData[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 8 - Database Connectivity & Schema</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <h1>StudentHub Portal</h1>
        <p>Practical 8: MySQL Schema Design, ER Modeling & Connectivity</p>
    </header>

    <div class="card">
        <h2>Database Connection Status</h2>

        <div>
            <strong>MySQLi Connection:</strong>
            <span class="status-badge <?php echo $mysqliConnected ? 'status-success' : 'status-fail'; ?>">
                <?php echo htmlspecialchars($mysqliStatus); ?>
            </span>
        </div>

        <div>
            <strong>PDO Connection:</strong>
            <span class="status-badge <?php echo $pdoConnected ? 'status-success' : 'status-fail'; ?>">
                <?php echo htmlspecialchars($pdoStatus); ?>
            </span>
        </div>
    </div>

    <div class="card">
        <h2>Schema Overview</h2>
        <p>The StudentHub database is normalized into four core entities: Users, Students, Events, and Registrations.</p>

        <h3>Database Tables Detected</h3>
        <?php if (!empty($tableNames)): ?>
            <ul>
                <?php foreach ($tableNames as $tbl): ?>
                    <li><strong><?php echo htmlspecialchars($tbl); ?></strong> table</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Database created via schema.sql. Tables: <strong>users, students, events, registrations</strong>.</p>
        <?php endif; ?>

        <h3>Sample Query: Seeded Students (MySQL SELECT)</h3>
        <?php if (!empty($studentsData)): ?>
            <table class="table-schema">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Phone</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studentsData as $st): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($st['name']); ?></td>
                            <td><?php echo htmlspecialchars($st['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($st['branch']); ?></td>
                            <td><?php echo htmlspecialchars($st['semester']); ?></td>
                            <td><?php echo htmlspecialchars($st['phone']); ?></td>
                            <td><?php echo htmlspecialchars($st['attendance_pct']); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>To initialize the database, import <code>schema.sql</code> in phpMyAdmin or run MySQL on port 3306.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>SQL Setup Query</h2>
        <div class="code-box">mysql -u root -p &lt; schema.sql</div>
    </div>

</body>
</html>
