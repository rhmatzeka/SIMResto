// File: manajer/laporan_menu.php
<?php
session_start();
require_once '../unauthorized.php';
check_role(['manajer']);
require_once '../koneksi.php';

// Query untuk mendapatkan menu beserta total penjualan
$query = "SELECT 
            mi.menu_item_id,
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Menu - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .best-seller {
            background-color: #fff3cd; /* Highlight untuk best seller */
        }
        .poor-seller {
            background-color: #f8d7da; /* Highlight untuk menu yang kurang laku */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <!-- ... (sama seperti dashboard manajer) ... -->

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Laporan Penjualan Menu</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="ekspor_laporan.php?type=menu" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-file-export"></i> Ekspor Excel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Laporan -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Menu</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Terjual</th>
                                        <th>Total Pendapatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        $class = '';
                                        if ($row['total_terjual'] > 50) {
                                            $class = 'best-seller';
                                        } elseif ($row['total_terjual'] < 5) {
                                            $class = 'poor-seller';
                                        }
                                        echo "<tr class='{$class}'>";
                                        echo "<td>{$no}</td>";
                                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                        echo "<td>Rp " . number_format($row['price'], 2) . "</td>";
                                        echo "<td>" . $row['total_terjual'] . "</td>";
                                        echo "<td>Rp " . number_format($row['total_pendapatan'], 2) . "</td>";
                                        echo "<td>
                                                <a href='edit_menu.php?id={$row['menu_item_id']}' class='btn btn-sm btn-warning'>
                                                    <i class='fas fa-edit'></i> Edit
                                                </a>
                                                <a href='detail_menu.php?id={$row['menu_item_id']}' class='btn btn-sm btn-info'>
                                                    <i class='fas fa-info-circle'></i> Detail
                                                </a>
                                            </td>";
                                        echo "</tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>