<?php
session_start();
require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>