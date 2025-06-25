<?php
require_once '../koneksi.php'; // Sesuaikan path

// Ambil data pesanan
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
}
$conn->close();
?>

<h3 class="border-bottom pb-2 mb-3">Pesanan Makanan Aktif (Pending)</h3>
<div class="row">
    <?php if (empty($orders)): ?>
        <p class="text-muted">Tidak ada pesanan aktif (pending).</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="col-md-6 mb-4">
                <div class="card order-card border-preparing shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Order #<?php echo htmlspecialchars($order['order_id']); ?> 
                            <span class="badge bg-warning text-dark status-badge"><?php echo htmlspecialchars($order['order_status']); ?></span>
                        </h5>
                        <p class="card-text"><strong>Pelanggan:</strong> <?php echo htmlspecialchars($order['customer_name'] ?: 'N/A'); ?></p>
                        <p class="card-text"><strong>Meja:</strong> <?php echo htmlspecialchars($order['table_number'] ?: 'Takeaway'); ?></p>
                        <p class="card-text"><strong>Waktu Pesan:</strong> <?php echo (new DateTime($order['order_date']))->format('d M Y H:i'); ?></p>
                        <h6>Items:</h6>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($order['items'])): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <?php
                                    $itemStatusBadge = '';
                                    $itemButton = '';
                                    if ($item['status'] === 'Pending' || $item['status'] === 'Preparing') {
                                        $itemStatusBadge = '<span class="badge bg-secondary status-badge">' . htmlspecialchars($item['status']) . '</span>';
                                    } elseif ($item['status'] === 'Ready') {
                                        $itemStatusBadge = '<span class="badge bg-info status-badge">' . htmlspecialchars($item['status']) . '</span>';
                                        $itemButton = '<button class="btn btn-sm btn-success ms-2" onclick="deliverItem(' . $item['order_item_id'] . ')">Done</button>';
                                    } elseif ($item['status'] === 'Delivered') {
                                        $itemStatusBadge = '<span class="badge bg-success status-badge">' . htmlspecialchars($item['status']) . '</span>';
                                    }
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['item_name']); ?> 
                                        <div>
                                            <?php echo $itemStatusBadge; ?>
                                            <?php echo $itemButton; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">Tidak ada item untuk pesanan ini.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>