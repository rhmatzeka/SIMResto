<?php
include 'admin_only.php';
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM menu_items WHERE menu_item_id = $id");
}

header('Location: data_menu.php');
exit;
?>