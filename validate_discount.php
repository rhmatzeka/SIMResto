<?php
require_once 'koneksi.php'; // Pastikan path ini benar

// PENTING: Atur zona waktu ke WIB agar pengecekan waktu akurat
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

// Respon default jika terjadi kesalahan
$response = ['success' => false, 'message' => 'Invalid request.'];

// Ambil data JSON yang dikirim dari chackout.js
$input = json_decode(file_get_contents('php://input'), true);
$kode_diskon = strtoupper($input['kode_diskon'] ?? '');
$subtotal = floatval($input['subtotal'] ?? 0);

if (empty($kode_diskon)) {
    $response['message'] = 'Kode diskon tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

// Query yang benar untuk mengecek diskon:
// 1. Cek kode diskon
// 2. Cek statusnya 'aktif'
// 3. Cek apakah waktu SEKARANG berada dalam periode waktu_mulai dan waktu_berakhir
$stmt = $conn->prepare("
    SELECT * FROM discounts 
    WHERE kode_diskon = ? 
      AND status = 'aktif'
      AND (waktu_mulai IS NULL OR NOW() >= waktu_mulai)
      AND (waktu_berakhir IS NULL OR NOW() <= waktu_berakhir)
");
$stmt->bind_param("s", $kode_diskon);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Jika diskon valid, hitung potongannya
    $discount = $result->fetch_assoc();
    $discount_amount = 0;

    if ($discount['tipe_diskon'] == 'persen') {
        $discount_amount = $subtotal * ($discount['nilai_diskon'] / 100);
    } else { // tipe 'tetap'
        $discount_amount = $discount['nilai_diskon'];
    }

    if ($discount_amount > $subtotal) {
        $discount_amount = $subtotal;
    }

    // Kirim respon sukses
    $response = [
        'success' => true,
        'message' => 'Diskon "' . htmlspecialchars($discount['deskripsi']) . '" berhasil diterapkan!',
        'discount_amount' => round($discount_amount, 2)
    ];

} else {
    // Jika diskon tidak ditemukan atau tidak valid
    $response['message'] = 'Kode diskon tidak valid atau sudah tidak berlaku.';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>