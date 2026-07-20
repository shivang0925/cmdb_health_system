<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'Admin') {
    die("Access denied: only an Admin can delete Configuration Items.");
}
?>
<?php

include("includes/db_connect.php");

$id = $_GET['id'];

mysqli_query(
    $conn,
    "DELETE FROM configuration_items WHERE ci_id=$id"
);

header("Location: view_ci.php");

?>