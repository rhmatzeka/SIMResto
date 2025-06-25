<?php
header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$json_payload = file_get_contents('php://input');
$data = json_decode($json_payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data. Error: ' . json_last_error_msg()]);
    exit;
}

if (!isset($data['items']) || !is_array($data['items']) || empty($data['items']) || !isset($data['grandTotal']) || !isset($data['paymentMethod'])) {
    echo json_encode(['success' => false, 'message' => 'Missing or empty required fields in order data.']);
    exit;
}

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'User session not found. Please login again.']);
    exit;
}
$user_id_session = $_SESSION['user']['id'];

$order_items_payload = $data['items'];
$grand_total = floatval($data['grandTotal']);
$payment_method_payload = htmlspecialchars($data['paymentMethod']);
$discount_code = $data['discountCode'] ?? null;
$discount_amount = floatval($data['discountAmount'] ?? 0);

require_once 'koneksi.php';

$conn->begin_transaction();

try {
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, order_status, order_date, discount_code, discount_amount) VALUES (?, ?, ?, 'Pending', NOW(), ?, ?)");
    $stmt_order->bind_param("idsdd", $user_id_session, $grand_total, $payment_method_payload, $discount_code, $discount_amount);
    $stmt_order->execute();
    
    $new_order_id_generated = $conn->insert_id;
    if ($new_order_id_generated == 0) {
        throw new Exception("Failed to create order in 'orders' table.");
    }
    $stmt_order->close();

    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, price_per_item, subtotal) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($order_items_payload as $item_data) {
        // =================================================================
        // PERBAIKAN FINAL ADA DI 2 BARIS INI
        // Menyesuaikan 'key' yang dibaca dengan 'key' dari Payload
        // =================================================================
        $menu_id_item = intval($item_data['menu_item_id'] ?? 0); // Diubah dari 'id' menjadi 'menu_item_id'
        $price_per_item_payload = floatval($item_data['price_per_unit'] ?? 0.0); // Diubah dari 'price' menjadi 'price_per_unit'
        
        // Variabel lain yang sudah benar
        $item_name_from_payload = htmlspecialchars($item_data['name'] ?? 'Unknown Item');
        $quantity_item = intval($item_data['quantity'] ?? 0);
        $subtotal_item_value = floatval($item_data['subtotal'] ?? 0.0);

        if ($menu_id_item <= 0 || $quantity_item <= 0) {
             continue; // Lewati item yang tidak valid
        }
        
        $stmt_items->bind_param("iisidd", $new_order_id_generated, $menu_id_item, $item_name_from_payload, $quantity_item, $price_per_item_payload, $subtotal_item_value);
        $stmt_items->execute();
    }
    $stmt_items->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $new_order_id_generated,
        'message' => 'Order placed successfully!'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Order processing exception: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your order.',
        'debug_info' => $e->getMessage() 
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>