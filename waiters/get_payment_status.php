<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$payment_statuses = [];

// Ambil semua pesanan yang belum Completed atau Paid (misal status 'Pending', atau 'Processing')
// dan dikelompokkan berdasarkan meja
$query = "
    SELECT 
        o.table_number, 
        SUM(o.total_price) as total_unpaid_amount,
        COUNT(o.order_id) as total_pending_orders,
        GROUP_CONCAT(o.order_id) AS order_ids
    FROM orders o
    WHERE o.order_status = 'Pending' -- Asumsi 'Pending' berarti belum bayar
    AND o.table_number IS NOT NULL
    GROUP BY o.table_number
    ORDER BY o.table_number ASC
";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $payment_statuses[] = $row;
    }
} else {
    error_log("Error in get_payment_status.php: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil status pembayaran.']);
    exit();
}

$conn->close();
echo json_encode($payment_statuses);
exit();
?>