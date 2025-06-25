<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_lamperieresto";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
$conn->query("SET time_zone = '+07:00'");
?>
