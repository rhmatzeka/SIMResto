<?php
require_once '../koneksi.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];

$action = $_GET['action'] ?? '';

// Action baru untuk mengkonfirmasi kedatangan pelanggan
if ($action == 'confirm_arrival' && isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $current_datetime = date("Y-m-d H:i:s");

    // Hanya ubah status dari 'Confirmed' menjadi 'Arrived'
    // dan pastikan table_number sudah terisi dari data reservasi awal
    $stmt = $conn->prepare("UPDATE reservations SET status = 'Arrived', seated_at = ? WHERE id = ? AND status = 'Confirmed'");
    $stmt->bind_param("si", $current_datetime, $reservation_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = ['status' => 'success', 'message' => 'Kedatangan pelanggan berhasil dikonfirmasi.'];
        } else {
            $response['message'] = 'Gagal mengkonfirmasi kedatangan atau reservasi tidak berstatus "Confirmed".';
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $conn->error];
    }
    $stmt->close();

} elseif ($action == 'complete' && isset($_GET['id'])) {
    // Action untuk menyelesaikan reservasi (mengosongkan meja)
    $reservation_id = intval($_GET['id']);
    
    // Meja hanya dikosongkan jika statusnya 'Arrived' atau 'Seated'
    $stmt = $conn->prepare("UPDATE reservations SET status = 'Completed', table_number = NULL WHERE id = ? AND status IN ('Arrived', 'Seated')");
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = ['status' => 'success', 'message' => 'Reservasi telah diselesaikan. Meja kembali tersedia.'];
        } else {
            $response['message'] = 'Gagal menyelesaikan atau reservasi tidak dalam status "Arrived" atau "Seated".';
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Terjadi kesalahan saat mengupdate database: ' . $conn->error];
    }
    $stmt->close();

} else {
    $response = ['status' => 'error', 'message' => 'Aksi tidak valid atau tidak diizinkan.'];
}

echo json_encode($response);
$conn->close();
exit();
?>