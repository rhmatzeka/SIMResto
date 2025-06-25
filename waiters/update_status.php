<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deliver' && isset($_POST['item_id'])) {
    $order_item_id = intval($_POST['item_id']);

    if ($order_item_id > 0) {
        $stmt = $conn->prepare("UPDATE order_items SET status = 'Delivered' WHERE order_item_id = ? AND status = 'Ready'");
        $stmt->bind_param("i", $order_item_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = ['status' => 'success', 'message' => 'Item pesanan berhasil ditandai selesai.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal memperbarui status item atau item tidak berstatus "Ready".'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Error database saat update status: ' . $conn->error];
        }
        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'ID Item Pesanan tidak valid.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Permintaan tidak valid.'];
}

$conn->close();
echo json_encode($response);
exit();
?>