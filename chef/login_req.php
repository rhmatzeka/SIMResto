<?php
session_start();

function check_login() {
    if (!isset($_SESSION['user']['id'])) {
        header('Location: ../login.php'); // <-- PERBAIKAN DI SINI (langsung nama file)
        exit;
    }
}

function check_role($allowed_roles) {
    if (!isset($_SESSION['user']['role']) || !in_array($_SESSION['user']['role'], $allowed_roles)) {
        header('Location: ../logout.php'); // <-- PERBAIKAN DI SINI (langsung nama file)
        exit;
    }
}

check_login();
?>