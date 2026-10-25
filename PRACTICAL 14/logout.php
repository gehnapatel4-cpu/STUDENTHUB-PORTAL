<?php
require_once "auth.php";

if (isset($_SESSION["username"])) {
    logAuditAction($conn, "USER_LOGOUT");
}

session_unset();
session_destroy();

header("Location: login.php");
exit();
?>
