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
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    // Handle file upload
    $image_url = null;
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../images/menu/';
        $file_name = basename($_FILES['image_url']['name']);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $file_path)) {
            $image_url = 'images/menu/' . $file_name;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO menu_items (item_name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $item_name, $description, $price, $category, $image_url);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Menu berhasil ditambahkan!";
        header("Location: manajemen_menu.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan menu: " . $conn->error;
        header("Location: manajemen_menu.php");
        exit();
    }
} else {
    header("Location: manajemen_menu.php");
    exit();
}
?>