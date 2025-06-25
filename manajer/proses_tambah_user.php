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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    
    // Generate member_id - improved method
    $year = date('Y');
    
    // Find the highest existing member_id number for the current year
    // Kode BARU yang sudah diperbaiki
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(member_id, 9) AS UNSIGNED)) as max_num 
                       FROM users 
                       WHERE member_id LIKE ?");
    $like_pattern = 'LP-' . $year . '-%';
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $max_num = $row['max_num'] ? $row['max_num'] : 0;
    $next_num = $max_num + 1;
    $member_id = 'LP-' . $year . '-' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role, member_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $phone, $role, $member_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User berhasil ditambahkan!";
        header("Location: manajemen_user.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan user: " . $conn->error;
        header("Location: manajemen_user.php");
        exit();
    }
} else {
    header("Location: manajemen_user.php");
    exit();
}
?>