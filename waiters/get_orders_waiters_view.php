<?php
require_once '../koneksi.php';



$query = "
    SELECT 
        o.order_id,
        o.customer_name,
        o.table_number,
        o.order_date,
        o.total_price,
        GROUP_CONCAT(
            CONCAT(oi.order_item_id, '||', oi.item_name, '||', oi.quantity, '||', oi.status)
            SEPARATOR '~~~'
        ) AS items_data
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_status = 'pending'
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
            $items_raw = explode('~~~', $row['items_data']);
            foreach ($items_raw as $str) {
                $parts = explode('||', $str);
                if (count($parts) === 4) {
                    $order['items'][] = [
                        'order_item_id' => (int)$parts[0],
                        'item_name'     => $parts[1],
                        'quantity'      => (int)$parts[2],
                        'status'        => $parts[3]
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

<h3 class="border-bottom pb-3 mb-4 text-warning">
    Pesanan Aktif (Menunggu Pengantaran)
</h3>

<?php if (empty($orders)): ?>
    <div class="text-center py-5">
        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
        <h4 class="text-muted">Semua pesanan sudah diantar!</h4>
        <p>Kerja bagus, tim!</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($orders as $order): ?>
            <div class="col-lg-6">
                <div class="card border-warning shadow-lg h-100">
                    <div class="card-header bg-warning text-dark fw-bold">
                        Order #<?= $order['order_id'] ?>
                        <span class="badge bg-dark float-end">Meja <?= htmlspecialchars($order['table_number'] ?: 'Takeaway') ?></span>
                    </div>
                    <div class="card-body">
                        <p class="card-text mb-1">
                            <strong>Pelanggan:</strong> <?= htmlspecialchars($order['customer_name'] ?: 'Walk-in') ?>
                        </p>
                        <p class="card-text mb-3 text-muted small">
                            <i class="far fa-clock"></i> 
                            <?= (new DateTime($order['order_date']))->format('d M Y H:i') ?>
                        </p>

                        <h6 class="fw-bold text-primary mb-3">Daftar Item:</h6>
                        <div class="list-group list-group-flush">
                            <?php foreach ($order['items'] as $item): ?>
                                <?php
                                $badge = $button = '';
                                if ($item['status'] === 'Ready') {
                                    $badge = '<span class="badge bg-info text-white">Siap</span>';
                                    $button = '<button class="button" class="btn btn-success btn-sm ms-2" onclick="deliverItem(' . $item['order_item_id'] . ')">
                                                    Antar
                                               </button>';
                                } elseif ($item['status'] === 'Delivered') {
                                    $badge = '<span class="badge bg-success">Sudah Diantar</span>';
                                } else {
                                    $badge = '<span class="badge bg-secondary">' . $item['status'] . '</span>';
                                }
                                ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div>
                                        <strong><?= $item['quantity'] ?>×</strong> <?= htmlspecialchars($item['item_name']) ?>
                                    </div>
                                    <div class="text-end">
                                        <?= $badge ?>
                                        <?= $button ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <strong>Total: Rp <?= number_format($order['total_price'], 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>

    
// Auto refresh tiap 8 detik biar selalu update dari kitchen
setInterval(() => {
    if (document.querySelector('#sidebar .nav-link.active')?.dataset.page === 'pending_orders') {
        loadContent('pending_orders');
    }
}, 8000);
</script>