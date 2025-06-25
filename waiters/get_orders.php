<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$query = "
    SELECT 
        o.order_id, 
        o.customer_name, 
        o.table_number, 
        o.order_date, 
        o.total_price, 
        o.order_status,
        GROUP_CONCAT(oi.order_item_id, ':', oi.item_name, ':', oi.quantity, ':', oi.status SEPARATOR '|||') AS items_data
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_status = 'Pending'
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";

$result = $conn->query($query);
$orders = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $order = $row;
        $order['items'] = [];
        if (!empty($row['items_data'])) {
            $items_raw = explode('|||', $row['items_data']);
            foreach ($items_raw as $item_str) {
                $item_parts = explode(':', $item_str);
                if (count($item_parts) === 4) {
                    list($item_id, $item_name, $quantity, $status) = $item_parts;
                    $order['items'][] = [
                        'order_item_id' => (int)$item_id,
                        'item_name' => $item_name,
                        'quantity' => (int)$quantity,
                        'status' => $status
                    ];
                }
            }
        }
        unset($order['items_data']);
        $orders[] = $order;
    }
} else {
    error_log("Error in get_orders.php: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data pesanan.']);
    exit();
}

$conn->close();
echo json_encode($orders);
exit();
?>