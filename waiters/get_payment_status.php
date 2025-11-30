<?php
// get_payment_status.php  →  VERSI PALING AMAN (copy ini kalau masih error)
require_once '../koneksi.php';
header('Content-Type: application/json');

$data = [];

$query = "SELECT table_number, total_price FROM orders WHERE LOWER(order_status) = 'pending' AND table_number IS NOT NULL";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $totals = [];
    while ($row = $result->fetch_assoc()) {
        $table = $row['table_number'];
        if (!isset($totals[$table])) {
            $totals[$table] = ['count' => 0, 'amount' => 0];
        }
        $totals[$table]['count']++;
        $totals[$table]['amount'] += (float)$row['total_price'];
    }

    foreach ($totals as $table => $info) {
        $data[] = [
            'table_number'         => $table,
            'total_unpaid_amount'  => $info['amount'],
            'total_pending_orders' => $info['count'],
            'order_ids'            => '' // kosongin aja gapapa
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'data'   => $data
]);

$conn->close();
?>