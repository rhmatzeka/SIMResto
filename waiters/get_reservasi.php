<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$date_now_start = date('Y-m-d 00:00:00');
$date_now_end = date('Y-m-d 23:59:59');

$stmt = $conn->prepare("
    SELECT id, customer_name, reservation_datetime, num_of_people, table_number, special_request, status, seated_at
    FROM reservations
    WHERE 
        (reservation_datetime BETWEEN ? AND ?) OR 
        (status IN ('Confirmed', 'Arrived', 'Seated')) -- Tambah 'Arrived' dan 'Seated' jika ingin melihat reservasi yang sudah datang dari hari sebelumnya atau yang masih aktif
    ORDER BY reservation_datetime ASC
");
$stmt->bind_param("ss", $date_now_start, $date_now_end);
$stmt->execute();
$result = $stmt->get_result();

$reservasi = [];
while ($row = $result->fetch_assoc()) {
    $reservasi[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($reservasi);
exit();
?>