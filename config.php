<?php
$host = "localhost";
$user = "root";   // default XAMPP username
$pass = "";       // default XAMPP password (empty)
$db = "bus_db";   // database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>