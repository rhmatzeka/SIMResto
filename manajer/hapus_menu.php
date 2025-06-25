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
    $menu_item_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE menu_item_id = ?");
    $stmt->bind_param("i", $menu_item_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Menu berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus menu: " . $conn->error;
    }
}

header("Location: manajemen_menu.php");
exit();
?>