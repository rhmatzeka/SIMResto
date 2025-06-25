<?php
session_start();
require_once '../unauthorized.php';
check_role(['manajer']);
require_once '../koneksi.php';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_penjualan_menu.xls"');

$query = "SELECT 
            mi.item_name,
            mi.category,
            mi.price,
            COALESCE(SUM(od.quantity), 0) AS total_terjual,
            COALESCE(SUM(od.quantity * od.price_per_item), 0) AS total_pendapatan
          FROM menu_items mi
          LEFT JOIN order_details od ON mi.menu_item_id = od.menu_item_id
          LEFT JOIN orders o ON od.order_id = o.order_id
          GROUP BY mi.menu_item_id
          ORDER BY total_terjual DESC";

$result = $conn->query($query);
?>

<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Menu</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Total Terjual</th>
        <th>Total Pendapatan</th>
    </tr>
    <?php
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$no}</td>";
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . $row['total_terjual'] . "</td>";
        echo "<td>" . number_format($row['total_pendapatan'], 2) . "</td>";
        echo "</tr>";
        $no++;
    }
    ?>
</table>