<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeoutDuration = 900;

if (isset($_SESSION["last_activity"])) {
    if ((time() - $_SESSION["last_activity"]) > $timeoutDuration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}
$_SESSION["last_activity"] = time();

function requireLogin() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php?unauthorized=1");
        exit();
    }
}

function requireRole($allowedRole) {
    requireLogin();
    if ($_SESSION["role"] !== $allowedRole) {
        header("Location: dashboard.php?forbidden=1");
        exit();
    }
}
?>
