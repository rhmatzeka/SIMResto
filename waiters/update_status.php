<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? '';

// ========================================
// 1. DELIVER ITEM (tombol "Antar" di Pesanan Pending)
// ========================================
if ($action === 'deliver' && !empty($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];

    $stmt = $conn->prepare("UPDATE order_items SET status = 'Delivered' WHERE order_item_id = ? AND status IN ('Ready', 'Preparing')");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => 'Item berhasil diantar!'];
    } else {
        $response = ['status' => 'warning', 'message' => 'Item sudah diantar atau sedang diproses.'];
    }
    $stmt->close();
}

// ========================================
// 2. BAYAR MEJA (tombol "Sudah Dibayar" di Status Pembayaran)
// ========================================
elseif ($action === 'pay_table' && !empty($_POST['table'])) {
    $table = $_POST['table']; // bisa string atau int, tergantung kolom table_number

    // Update semua order di meja itu jadi 'paid'
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'paid' WHERE table_number = ? AND order_status = 'pending'");
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $updated = $stmt->affected_rows;
    $stmt->close();

    // Kosongkan meja
    $stmt2 = $conn->prepare("UPDATE tables SET status = 'available' WHERE table_number = ?");
    $stmt2->bind_param("s", $table);
    $stmt2->execute();
    $stmt2->close();

    if ($updated > 0) {
        $response = ['status' => 'success', 'message' => "Meja $table telah LUNAS dan dibersihkan!"];
    } else {
        $response = ['status' => 'info', 'message' => "Tidak ada tagihan pending di meja $table."];
    }
}

// ========================================
// OUTPUT
// ========================================
echo json_encode($response);
$conn->close();
exit();