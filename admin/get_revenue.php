<?php
require '../koneksi.php'; // Sesuaikan path jika perlu

// Query untuk menghitung total pendapatan HANYA UNTUK HARI INI
// CURDATE() secara otomatis mengambil tanggal hari ini di server database
$queryRevenue = "SELECT SUM(total_price) as total_revenue 
                 FROM orders 
                 WHERE DATE(order_date) = CURDATE()";

$resultRevenue = mysqli_query($conn, $queryRevenue);
$revenueData = mysqli_fetch_assoc($resultRevenue);
$totalRevenueUsd = $revenueData['total_revenue'] ?? 0;

// Format angka menjadi format Dolar yang benar
$formattedRevenue = "$ " . number_format($totalRevenueUsd, 2, '.', ',');

// Kirim data sebagai JSON
header('Content-Type: application/json');
echo json_encode(['formatted_revenue' => $formattedRevenue]);
?>