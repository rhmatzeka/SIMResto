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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_item_id = $_POST['menu_item_id'];
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    $stmt = $conn->prepare("UPDATE menu_items SET item_name = ?, description = ?, price = ?, category = ? WHERE menu_item_id = ?");
    $stmt->bind_param("ssdsi", $item_name, $description, $price, $category, $menu_item_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Menu berhasil diperbarui!";
        header("Location: manajemen_menu.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui menu: " . $conn->error;
        header("Location: manajemen_menu.php");
        exit();
    }
} else {
    header("Location: manajemen_menu.php");
    exit();
}
?>