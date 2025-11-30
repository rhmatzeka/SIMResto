<?php
session_start();
require_once '../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'waiters') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak!']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !is_array($data['items']) || empty($data['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data pesanan tidak valid.']);
    exit;
}

$customer_name  = trim($data['customer_name'] ?? 'Pelanggan Offline');
$table_number    = trim($data['table_number'] ?? '');
$payment_method  = trim($data['payment_method'] ?? '');
$items           = $data['items'];
$user_id         = $_SESSION['user']['id'];

if (empty($table_number) || empty($payment_method)) {
    echo json_encode(['status' => 'error', 'message' => 'Meja dan metode pembayaran wajib diisi.']);
    exit;
}

$conn->begin_transaction();

try {
    $total_price = 0;
    foreach ($items as $it) {
        $total_price += (float)$it['price'] * (int)$it['quantity'];
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, table_number, customer_name, total_price, payment_method, order_status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("isssd", $user_id, $table_number, $customer_name, $total_price, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Insert items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, price_per_item, subtotal, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    foreach ($items as $it) {
        $mid = (int)$it['menu_item_id'];
        $name = htmlspecialchars($it['item_name']);
        $qty = (int)$it['quantity'];
        $price = (float)$it['price'];
        $sub = $price * $qty;
        $stmt->bind_param("iisidd", $order_id, $mid, $name, $qty, $price, $sub);
        $stmt->execute();
    }
    $stmt->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => "Pesanan Meja $table_number berhasil dibuat!"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
}

$conn->close();
?>