<?php
session_start();

// Cek apakah user sudah login dan memiliki role manajer
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'manajer') {
    header("Location: unauthorized.php");
    exit();
}

require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Jangan izinkan menghapus diri sendiri
    if ($id == $_SESSION['user']['id']) {
        $_SESSION['error_message'] = "Anda tidak dapat menghapus akun sendiri!";
        header("Location: manajemen_user.php");
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus user: " . $conn->error;
    }
}

header("Location: manajemen_user.php");
exit();
?>