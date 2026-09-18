<?php
$conn = mysqli_connect("localhost", "root", "", "cmdb_health");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>