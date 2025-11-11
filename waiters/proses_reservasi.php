<?php
require_once '../koneksi.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];

$action = $_GET['action'] ?? '';
$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($reservation_id <= 0) {
    echo json_encode($response);
    exit();
}

// ================================
// KONFIRMASI KEDATANGAN
// ================================
if ($action === 'confirm_arrival') {
    $current_datetime = date("Y-m-d H:i:s");

    // Ambil table_number agar bisa dicek apakah valid
    $check = $conn->prepare("SELECT table_number, status FROM reservations WHERE id = ?");
    $check->bind_param("i", $reservation_id);
    $check->execute();
    $result = $check->get_result();
    $res = $result->fetch_assoc();
    $check->close();

    if (!$res) {
        $response['message'] = 'Reservasi tidak ditemukan.';
    } elseif ($res['status'] !== 'Confirmed') {
        $response['message'] = 'Hanya reservasi dengan status Confirmed yang bisa dikonfirmasi kedatangannya.';
    } elseif (empty($res['table_number'])) {
        $response['message'] = 'Nomor meja belum ditentukan. Silakan atur meja terlebih dahulu.';
    } else {
        // Update status ke Arrived dan isi waktu seated_at
        $stmt = $conn->prepare("UPDATE reservations SET status = 'Arrived', seated_at = ? WHERE id = ?");
        $stmt->bind_param("si", $current_datetime, $reservation_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $response = ['status' => 'success', 'message' => 'Kedatangan pelanggan berhasil dikonfirmasi.'];
        } else {
            $response['message'] = 'Gagal memperbarui status reservasi.';
        }
        $stmt->close();
    }

// ================================
// SELESAIKAN RESERVASI
// ================================
} elseif ($action === 'complete') {
    $stmt = $conn->prepare("
        UPDATE reservations 
        SET status = 'Completed', table_number = NULL 
        WHERE id = ? AND status IN ('Arrived', 'Seated')
    ");
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => 'Reservasi telah diselesaikan dan meja dikosongkan.'];
    } else {
        $response['message'] = 'Reservasi tidak dalam status yang bisa diselesaikan.';
    }
    $stmt->close();

} else {
    $response['message'] = 'Aksi tidak valid.';
}

echo json_encode($response);
$conn->close();
exit();
?>
