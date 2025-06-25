<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}
function check_role(array $allowed_roles) {
    if (!isset($_SESSION['user']['role']) || !in_array($_SESSION['user']['role'], $allowed_roles)) {
        die('Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
?>