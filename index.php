<?php
session_start();
include("includes/db_connect.php");

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>