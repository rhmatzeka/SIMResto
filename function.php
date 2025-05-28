<?php
// config/db.php sudah di-include di sini
require_once __DIR__ . '/db.php';

session_start();

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect jika sudah login
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: /index.php');
        exit();
    }
}

// Redirect jika belum login
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

// Tampilkan pesan error
function displayErrors($errors) {
    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}
?>