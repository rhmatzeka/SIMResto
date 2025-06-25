<?php
require_once '../koneksi.php';

header('Content-Type: application/json');

// Ambil semua nomor meja dari tabel 'tables'
$all_tables_from_db = [];
$stmt_tables = $conn->prepare("SELECT table_number FROM tables ORDER BY table_number ASC");
$stmt_tables->execute();
$result_tables = $stmt_tables->get_result();
while ($row = $result_tables->fetch_assoc()) {
    $all_tables_from_db[] = $row['table_number'];
}
$stmt_tables->close();

// Cari meja yang sedang digunakan/dipesan untuk reservasi `Arrived` atau `Seated`
$occupied_or_reserved_tables = [];
$stmt_reserved = $conn->prepare("SELECT DISTINCT table_number FROM reservations WHERE status IN ('Arrived', 'Seated') AND table_number IS NOT NULL");
$stmt_reserved->execute();
$result_reserved = $stmt_reserved->get_result();
while ($row = $result_reserved->fetch_assoc()) {
    $occupied_or_reserved_tables[] = $row['table_number'];
}
$stmt_reserved->close();

// Filter meja yang sedang tidak digunakan untuk reservasi aktif
$available_tables_for_order = array_diff($all_tables_from_db, $occupied_or_reserved_tables);

echo json_encode(array_values($available_tables_for_order));
$conn->close();
exit();
?>