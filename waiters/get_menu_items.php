<?php
require_once '../koneksi.php'; // Sesuaikan path

header('Content-Type: application/json');

// Ambil kolom yang diperlukan, termasuk 'category'
$query = "SELECT menu_item_id, item_name, price, category FROM menu_items ORDER BY category ASC, item_name ASC";
$result = $conn->query($query);

$categorized_menu_items = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        // Jika kategori belum ada di array, inisialisasi sebagai array kosong
        if (!isset($categorized_menu_items[$category])) {
            $categorized_menu_items[$category] = [];
        }
        // Tambahkan item ke kategori yang sesuai
        $categorized_menu_items[$category][] = $row;
    }
} else {
    // Tangani error jika query gagal
    error_log("Error in get_menu_items.php: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data menu.']);
    exit();
}

$conn->close();
// Kembalikan array asosiatif di mana key adalah nama kategori
echo json_encode($categorized_menu_items);
exit();
?>