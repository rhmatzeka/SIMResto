<?php
// Menggunakan file keamanan dari folder yang sama
include 'login_required.php';
// Memastikan hanya role kasir atau admin yang bisa menghapus
check_role(['kasir', 'admin']);

// Menghubungkan ke database
include '../koneksi.php';

// Periksa apakah ID pesanan dikirim melalui URL
if (isset($_GET['id'])) {
    
    // Ambil dan bersihkan ID untuk keamanan
    $order_id = (int)$_GET['id'];

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    
    // Jalankan perintah hapus
    $stmt->execute();
    
    $stmt->close();
}

// Setelah selesai (atau jika tidak ada ID), kembalikan pengguna ke halaman daftar pesanan
header('Location: orders_admin.php');
exit;