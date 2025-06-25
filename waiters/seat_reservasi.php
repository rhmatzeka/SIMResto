<?php
require_once '../koneksi.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = intval($_POST['reservation_id'] ?? 0);
    $table_number = intval($_POST['table_number'] ?? 0);

    if ($reservation_id > 0 && $table_number > 0) {
        $stmt = $conn->prepare("UPDATE reservations SET status = 'Seated', table_number = ? WHERE id = ? AND status = 'Confirmed'");
        $stmt->bind_param("ii", $table_number, $reservation_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = ['status' => 'success', 'message' => "Pelanggan berhasil ditempatkan di meja nomor $table_number."];
            } else {
                $response['message'] = 'Reservasi tidak ditemukan atau statusnya bukan "Confirmed".';
            }
        } else {
            $response['message'] = 'Gagal mengupdate database.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'ID Reservasi atau Nomor Meja tidak valid.';
    }
}

echo json_encode($response);
$conn->close();
?>