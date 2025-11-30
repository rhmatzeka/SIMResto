<?php
session_start();
include 'koneksi.php';

// Set header agar output selalu JSON
header('Content-Type: application/json');

// 1. Cek Login
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login']);
    exit;
}

// 2. Ambil data JSON dari JavaScript
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input || empty($input['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Keranjang kosong atau data tidak valid']);
    exit;
}

// Ambil data user dan total
$userId = $_SESSION['user']['id'];
$totalPrice = $input['total'];
$cart = $input['cart'];
$orderDate = date('Y-m-d H:i:s');

// Set nilai default untuk kolom wajib lainnya
$totalAmount = $totalPrice; // Asumsi amount sama dengan price
$paymentMethod = "Cash";    // Default metode pembayaran

// 3. Simpan ke tabel 'orders'
// Kolom yang diisi: user_id, order_date, total_amount, total_price, payment_method, order_status
$stmt = $conn->prepare("INSERT INTO orders (user_id, order_date, total_amount, total_price, payment_method, order_status) VALUES (?, ?, ?, ?, ?, 'pending')");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error (orders): ' . $conn->error]);
    exit;
}

$stmt->bind_param("isdds", $userId, $orderDate, $totalAmount, $totalPrice, $paymentMethod);

if ($stmt->execute()) {
    $orderId = $conn->insert_id; // Ambil ID pesanan baru

    // 4. Simpan item ke tabel 'order_items'
    // Kolom: order_id, menu_item_id, item_name, quantity, price_per_item, subtotal
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, price_per_item, subtotal) VALUES (?, ?, ?, ?, ?, ?)");

    if (!$stmtItem) {
        echo json_encode(['status' => 'error', 'message' => 'Database error (items): ' . $conn->error]);
        exit;
    }

    foreach ($cart as $id => $item) {
        $menuItemId = $id; // ID item dari cart JS adalah menu_item_id
        $subtotal = $item['price'] * $item['qty'];
        
        $stmtItem->bind_param("iisidd", $orderId, $menuItemId, $item['name'], $item['qty'], $item['price'], $subtotal);
        
        if ($stmtItem->execute()) {
            // 5. Tambahkan status item untuk Dapur (order_item_status)
            $orderItemId = $conn->insert_id;
            $conn->query("INSERT INTO order_item_status (order_item_id, status) VALUES ($orderItemId, 'pending')");
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dibuat!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan: ' . $stmt->error]);
}
?>