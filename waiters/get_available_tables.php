<?php
require_once '../koneksi.php';

header('Content-Type: application/json');

// 1. Definisikan total meja yang ada di restoran
$total_tables = 5;
$all_tables = range(1, $total_tables);

// 2. Cari semua meja yang saat ini statusnya 'Seated'
$occupied_tables = [];
$stmt = $conn->prepare("SELECT table_number FROM reservations WHERE status = 'Seated'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['table_number'] !== null) {
        $occupied_tables[] = (int)$row['table_number'];
    }
}
$stmt->close();

// 3. Dapatkan meja yang tersedia
$available_tables = array_diff($all_tables, $occupied_tables);

// 4. Kembalikan hasilnya sebagai JSON
echo json_encode(array_values($available_tables)); 

$conn->close();
?>