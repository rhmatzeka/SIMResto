<?php
session_start();

// Cek apakah user sudah login dan memiliki role manajer
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'manajer') {
    header("Location: unauthorized.php");
    exit();
}

require_once '../koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .active {
            background-color: #007bff;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard_manajer.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="laporan_penjualan.php">
                                <i class="fas fa-chart-line me-2"></i>Laporan Penjualan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manajemen_menu.php">
                                <i class="fas fa-utensils me-2"></i>Manajemen Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manajemen_user.php">
                                <i class="fas fa-users me-2"></i>Manajemen User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Laporan Penjualan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">Semua Kategori</option>
                                        <?php
                                        $stmt = $conn->prepare("SELECT DISTINCT category FROM menu_items");
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Laporan Penjualan -->
                <div class="card">
                    <div class="card-header">
                        <h5>Data Penjualan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID Order</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Build query based on filters
                                    $query = "SELECT * FROM orders WHERE 1=1";
                                    $params = [];
                                    $types = "";
                                    
                                    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                                        $query .= " AND DATE(order_date) >= ?";
                                        $params[] = $_GET['start_date'];
                                        $types .= "s";
                                    }
                                    
                                    if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                                        $query .= " AND DATE(order_date) <= ?";
                                        $params[] = $_GET['end_date'];
                                        $types .= "s";
                                    }
                                    
                                    $query .= " ORDER BY order_date DESC";
                                    
                                    $stmt = $conn->prepare($query);
                                    
                                    if (!empty($params)) {
                                        $stmt->bind_param($types, ...$params);
                                    }
                                    
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['order_id'] . "</td>";
                                        echo "<td>" . date('d M Y H:i', strtotime($row['order_date'])) . "</td>";
                                        echo "<td>$ " . number_format($row['total_price'], 2) . "</td>";
                                        echo "<td>" . $row['payment_method'] . "</td>";
                                        echo "<td>" . $row['order_status'] . "</td>";
                                        echo "<td><a href='detail_order.php?id=" . $row['order_id'] . "' class='btn btn-sm btn-info'>Detail</a></td>";
                                        echo "</tr>";
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