<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../koneksi.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'tables' => [], 'message' => ''];

if (isset($_GET['datetime'], $_GET['people'])) {
    $datetime_str = $_GET['datetime'];
    $num_of_people = (int) $_GET['people'];

    try {
        $reservation_start = new DateTime($datetime_str);
        $reservation_end = clone $reservation_start;
        $reservation_end->modify('+2 hours');

        // Ambil semua meja yang cukup kapasitasnya
        $stmt = $conn->prepare("SELECT table_number, capacity FROM tables WHERE capacity >= ? ORDER BY table_number ASC");
        $stmt->bind_param("i", $num_of_people);
        $stmt->execute();
        $result = $stmt->get_result();

        $available_tables = [];

        while ($table = $result->fetch_assoc()) {
            $table_number = $table['table_number'];

            // Cek apakah meja ini sudah direservasi di waktu yang tumpang tindih
            $stmt_check = $conn->prepare("
                SELECT id FROM reservations 
                WHERE table_number = ? 
                AND status IN ('Pending', 'Confirmed') 
                AND (
                    (? < DATE_ADD(reservation_datetime, INTERVAL 2 HOUR)) 
                    AND (? > reservation_datetime)
                )
            ");
            $start_str = $reservation_start->format('Y-m-d H:i:s');
            $end_str = $reservation_end->format('Y-m-d H:i:s');
            $stmt_check->bind_param("sss", $table_number, $start_str, $end_str);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();

            $is_reserved = $check_result->num_rows > 0;

            $available_tables[] = [
                'table_number' => $table_number,
                'capacity' => $table['capacity'],
                'is_reserved' => $is_reserved
            ];

            $stmt_check->close();
        }

        $stmt->close();

        $response['status'] = 'success';
        $response['tables'] = $available_tables;
    } catch (Exception $e) {
        $response['message'] = 'Kesalahan sistem: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Parameter datetime dan people wajib diisi.';
}

$conn->close();
echo json_encode($response);
exit();
