<?php
session_start();
require_once '../unauthorized.php';
check_role(['manajer']);
require_once '../koneksi.php';

// Query untuk rekomendasi berdasarkan penjualan dan margin
$query = "SELECT 
            mi.menu_item_id,
            mi.item_name,
            mi.price,
            mi.category,
            COALESCE(SUM(od.quantity), 0) AS total_terjual,
            (mi.price * 0.7) AS estimasi_biaya, -- Asumsi 30% margin
            (mi.price - (mi.price * 0.7)) * COALESCE(SUM(od.quantity), 1) AS total_profit
          FROM menu_items mi
          LEFT JOIN order_details od ON mi.menu_item_id = od.menu_item_id
          LEFT JOIN orders o ON od.order_id = o.order_id
          GROUP BY mi.menu_item_id
          ORDER BY total_profit DESC
          LIMIT 10";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (head sama seperti sebelumnya) ... -->
</head>
<body>
    <div class="container-fluid">
        <!-- ... (sidebar sama seperti sebelumnya) ... -->
        
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Rekomendasi Menu</h1>
            </div>

            <div class="alert alert-info">
                <strong>Analisis Profitabilitas:</strong> Berikut rekomendasi menu berdasarkan kombinasi penjualan dan margin keuntungan.
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Kategori</th>
                                    <th>Harga Jual</th>
                                    <th>Estimasi Biaya</th>
                                    <th>Margin</th>
                                    <th>Total Terjual</th>
                                    <th>Total Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                    $margin = $row['price'] - $row['estimasi_biaya'];
                                    $margin_percent = ($margin / $row['price']) * 100;
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo "<td>Rp " . number_format($row['price'], 2) . "</td>";
                                    echo "<td>Rp " . number_format($row['estimasi_biaya'], 2) . "</td>";
                                    echo "<td>" . number_format($margin_percent, 2) . "%</td>";
                                    echo "<td>" . $row['total_terjual'] . "</td>";
                                    echo "<td>Rp " . number_format($row['total_profit'], 2) . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Rekomendasi Aksi</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Fokus promosi pada menu dengan profit tinggi</li>
                        <li>Evaluasi menu dengan penjualan rendah dan margin kecil</li>
                        <li>Pertimbangkan paket bundling untuk menu dengan margin tinggi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>