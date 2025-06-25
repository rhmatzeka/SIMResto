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
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];
    
    // Jika password baru diisi, update password
    if (!empty($new_password)) {
        $password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $role, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $role, $id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User berhasil diperbarui!";
        header("Location: manajemen_user.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui user: " . $conn->error;
        header("Location: manajemen_user.php");
        exit();
    }
} else {
    header("Location: manajemen_user.php");
    exit();
}
?>