<?php
session_start();
require_once '../koneksi.php';
header('Content-Type: application/json');

// Pastikan hanya waiters
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'waiters') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

if ($action === 'confirm_arrival') {
    $now = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        UPDATE reservations 
        SET status = 'Arrived', seated_at = ? 
        WHERE id = ? AND status IN ('Pending', 'Confirmed')
    ");
    $stmt->bind_param("si", $now, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update meja jadi reserved
        $conn->query("
            UPDATE tables t 
            JOIN reservations r ON t.table_number = r.table_number 
            SET t.status = 'reserved' 
            WHERE r.id = $id AND r.table_number IS NOT NULL
        ");
        echo json_encode(['status' => 'success', 'message' => 'Kedatangan berhasil dikonfirmasi!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Reservasi tidak ditemukan atau sudah diproses']);
    }
    $stmt->close();
}

elseif ($action === 'complete') {
    $stmt = $conn->prepare("
        UPDATE reservations 
        SET status = 'Completed' 
        WHERE id = ? AND status IN ('Arrived', 'Seated')
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->query("
            UPDATE tables t 
            JOIN reservations r ON t.table_number = r.table_number 
            SET t.status = 'available' 
            WHERE r.id = $id
        ");
        echo json_encode(['status' => 'success', 'message' => 'Reservasi selesai, meja sudah dibebaskan!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak bisa menyelesaikan reservasi']);
    }
    $stmt->close();
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);
}

$conn->close();
exit;