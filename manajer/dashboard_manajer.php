<?php
// Path yang benar: Keluar satu level (../) untuk menemukan file di folder utama
include 'unauthorized.php';
// Memastikan hanya role kasir atau admin yang bisa akses
check_role(['kasir', 'admin', 'manajer']);

include '../koneksi.php';

$manajerName = htmlspecialchars($_SESSION['user']['name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="laporan_penjualan.php">
                                <i class="fas fa-chart-line me-2"></i>Sales Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manajemen_menu.php">
                                <i class="fas fa-utensils me-2"></i>Menu Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manajemen_user.php">
                                <i class="fas fa-users me-2"></i>User Management
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
                    <h1 class="h2">Manager Dashboard</h1>
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

                <!-- Dashboard Content -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Sales by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Query data untuk chart
// Query data untuk chart (baris ~123)
$categoryQuery = "SELECT 
                    mi.category,
                    SUM(od.quantity) as total_terjual,
                    SUM(od.quantity * od.price_per_item) as total_pendapatan
                  FROM menu_items mi
                  JOIN order_items od ON mi.menu_item_id = od.menu_item_id
                  GROUP BY mi.category
                  ORDER BY total_terjual DESC";
$categoryResult = $conn->query($categoryQuery);

$categories = [];
$salesData = [];
$revenueData = [];

while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category'];
    $salesData[] = $row['total_terjual'];
    $revenueData[] = $row['total_pendapatan'];
}
?>

<script>
// Chart Penjualan per Kategori
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [{
            label: 'quantity sold',
            data: <?php echo json_encode($salesData); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' items sold';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'quantity sold'
                }
            }
        }
    }
});

// Chart Pendapatan per Kategori
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [{
            label: 'total income',
            data: <?php echo json_encode($revenueData); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '$ ' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>


<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Sales Trend (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="dailySalesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Query data trend harian
$dailyQuery = "SELECT 
                 DATE(o.order_date) as tanggal,
                 COUNT(DISTINCT o.order_id) as total_order,
                 SUM(od.quantity) as total_terjual,
                 SUM(od.quantity * od.price_per_item) as total_pendapatan
               FROM orders o
               JOIN order_items od ON o.order_id = od.order_id
               WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
               GROUP BY DATE(o.order_date)
               ORDER BY tanggal ASC";

$dailyResult = $conn->query($dailyQuery);

$dates = [];
$dailySales = [];
$dailyRevenue = [];

$exchange_rate_usd_to_idr = 15000; 

while ($row = $dailyResult->fetch_assoc()) {
    $dates[] = date('d M', strtotime($row['tanggal']));
    $dailySales[] = $row['total_terjual'];
    $dailyRevenue[] = $row['total_pendapatan']/$exchange_rate_usd_to_idr;
}
?>

<script>
// Chart Trend Harian
const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [
            {
                label: 'number of items sold',
                data: <?php echo json_encode($dailySales); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3,
                yAxisID: 'y'
            },
            {
                label: 'total income ($)',
                data: <?php echo json_encode($dailyRevenue); ?>,
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                tension: 0.3,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label.includes('Income')) {
                            return label + ': $ ' + context.parsed.y.toLocaleString();
                        } else {
                            return label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'number of items'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'income ($)'
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return '$ ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 vs Bottom 5 Menus Comparison</h5>
            </div>
            <div class="card-body">
                <canvas id="topBottomChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Query data top dan bottom menu
$topQuery = "SELECT mi.item_name, SUM(od.quantity) as total_terjual
             FROM menu_items mi
             JOIN order_items od ON mi.menu_item_id = od.menu_item_id
             GROUP BY mi.menu_item_id
             ORDER BY total_terjual DESC
             LIMIT 5";

$bottomQuery = "SELECT mi.item_name, COALESCE(SUM(od.quantity), 0) as total_terjual
                FROM menu_items mi
                LEFT JOIN order_items od ON mi.menu_item_id = od.menu_item_id
                GROUP BY mi.menu_item_id
                ORDER BY total_terjual ASC
                LIMIT 5";

$topResult = $conn->query($topQuery);
$bottomResult = $conn->query($bottomQuery);

$topLabels = [];
$topData = [];
$bottomLabels = [];
$bottomData = [];

while ($row = $topResult->fetch_assoc()) {
    $topLabels[] = $row['item_name'];
    $topData[] = $row['total_terjual'];
}

while ($row = $bottomResult->fetch_assoc()) {
    $bottomLabels[] = $row['item_name'];
    $bottomData[] = $row['total_terjual'];
}
?>

<script>
// Chart Perbandingan Top vs Bottom
const topBottomCtx = document.getElementById('topBottomChart').getContext('2d');
const topBottomChart = new Chart(topBottomCtx, {
    type: 'bar',
    data: {
        labels: [...<?php echo json_encode($topLabels); ?>, ...<?php echo json_encode($bottomLabels); ?>],
        datasets: [
            {
                label: 'Top Selling Menu',
                data: [...<?php echo json_encode($topData); ?>, ...Array(<?php echo count($bottomLabels); ?>).fill(null)],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Least Selling Menu',
                data: [...Array(<?php echo count($topLabels); ?>).fill(null), ...<?php echo json_encode($bottomData); ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' item terjual';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'quantity sold'
                }
            }
        }
    }
});
</script>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Payment Method Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="paymentMethodChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Total Revenue by Payment Method</h5>
            </div>
            <div class="card-body">
                <canvas id="paymentRevenueChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Query data metode pembayaran
$paymentQuery = "SELECT 
                   payment_method,
                   COUNT(*) as jumlah_transaksi,
                   SUM(total_price) as total_pendapatan
                 FROM orders
                 GROUP BY payment_method";

$paymentResult = $conn->query($paymentQuery);

$paymentMethods = [];
$paymentCounts = [];
$paymentRevenues = [];

while ($row = $paymentResult->fetch_assoc()) {
    $paymentMethods[] = $row['payment_method'];
    $paymentCounts[] = $row['jumlah_transaksi'];
    $paymentRevenues[] = $row['total_pendapatan'];
}
?>

<script>
// Chart Metode Pembayaran
const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
const paymentChart = new Chart(paymentCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($paymentMethods); ?>,
        datasets: [{
            data: <?php echo json_encode($paymentCounts); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.raw + ' transactions';
                    }
                }
            }
        }
    }
});

// Chart Pendapatan per Metode Pembayaran
const paymentRevCtx = document.getElementById('paymentRevenueChart').getContext('2d');
const paymentRevChart = new Chart(paymentRevCtx, {
    type: 'polarArea',
    data: {
        labels: <?php echo json_encode($paymentMethods); ?>,
        datasets: [{
            data: <?php echo json_encode($paymentRevenues); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $ ' + context.raw.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Today's Total Sales</h5>
                                <?php
                                $today = date('Y-m-d');
                                $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM orders WHERE DATE(order_date) = ?");
                                $stmt->bind_param("s", $today);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                $total = $row['total'] ? $row['total'] : 0;
                                ?>
                                <h2 class="card-text">$ <?php echo number_format($total, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Menus</h5>
                                <?php
                                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM menu_items");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                ?>
                                <h2 class="card-text"><?php echo $row['total']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <?php
                                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                ?>
                                <h2 class="card-text"><?php echo $row['total']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 Selling Menus</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT mi.item_name, SUM(od.quantity) as total_terjual, 
                                     SUM(od.quantity * od.price_per_item) as pendapatan
                                      FROM order_items od
                                      JOIN menu_items mi ON od.menu_item_id = mi.menu_item_id
                                      GROUP BY mi.menu_item_id
                                      ORDER BY total_terjual DESC
                                      LIMIT 5";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo "<td>" . $row['total_terjual'] . "</td>";
                                echo "<td>$ " . number_format($row['pendapatan'], 2) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Bottom 5 Selling Menus</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT mi.item_name, COALESCE(SUM(od.quantity), 0) as total_terjual, 
                                     COALESCE(SUM(od.quantity * od.price_per_item), 0) as pendapatan
                                      FROM menu_items mi
                                      LEFT JOIN order_items od ON mi.menu_item_id = od.menu_item_id
                                      GROUP BY mi.menu_item_id
                                      ORDER BY total_terjual ASC
                                      LIMIT 5";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo "<td>" . $row['total_terjual'] . "</td>";
                                echo "<td>$ " . number_format($row['pendapatan'], 2) . "</td>";
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
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Recent Sales</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID Order</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['order_id'] . "</td>";
                                        echo "<td>" . date('d M Y H:i', strtotime($row['order_date'])) . "</td>";
                                        echo "<td>$ " . number_format($row['total_price'], 2) . "</td>";
                                        echo "<td>" . $row['payment_method'] . "</td>";
                                        echo "<td>" . $row['order_status'] . "</td>";
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