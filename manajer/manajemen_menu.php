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
    <title>Manajemen Menu - LAMPERIE</title>
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
                            <a class="nav-link" href="laporan_penjualan.php">
                                <i class="fas fa-chart-line me-2"></i>Laporan Penjualan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manajemen_menu.php">
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
                    <h1 class="h2">Manajemen Menu</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahMenuModal">
                            <i class="fas fa-plus me-1"></i> Tambah Menu
                        </button>
                        <div class="dropdown ms-2">
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

                <!-- Daftar Menu -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Menu</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM menu_items ORDER BY category, item_name");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['menu_item_id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                        echo "<td>Rp " . number_format($row['price'], 2) . "</td>";
                                        echo "<td>
                                                <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editMenuModal' 
                                                    data-id='" . $row['menu_item_id'] . "' 
                                                    data-name='" . htmlspecialchars($row['item_name']) . "' 
                                                    data-desc='" . htmlspecialchars($row['description']) . "' 
                                                    data-price='" . $row['price'] . "' 
                                                    data-category='" . htmlspecialchars($row['category']) . "'>
                                                    <i class='fas fa-edit'></i> Edit
                                                </button>
                                                <a href='hapus_menu.php?id=" . $row['menu_item_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus menu ini?\")'>
                                                    <i class='fas fa-trash'></i> Hapus
                                                </a>
                                            </td>";
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

    <!-- Modal Tambah Menu -->
    <div class="modal fade" id="tambahMenuModal" tabindex="-1" aria-labelledby="tambahMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="proses_tambah_menu.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahMenuModalLabel">Tambah Menu Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="item_name" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Main Course">Main Course</option>
                                <option value="Appetizer">Appetizer</option>
                                <option value="Snacks">Snacks</option>
                                <option value="Dessert">Dessert</option>
                                <option value="Coffee">Coffee</option>
                                <option value="Non-Coffee">Non-Coffee</option>
                                <option value="Juice">Juice</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Gambar Menu</label>
                            <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="proses_edit_menu.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuModalLabel">Edit Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_menu_item_id" name="menu_item_id">
                        <div class="mb-3">
                            <label for="edit_item_name" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="edit_item_name" name="item_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category" class="form-label">Kategori</label>
                            <select class="form-select" id="edit_category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Main Course">Main Course</option>
                                <option value="Appetizer">Appetizer</option>
                                <option value="Snacks">Snacks</option>
                                <option value="Dessert">Dessert</option>
                                <option value="Coffee">Coffee</option>
                                <option value="Non-Coffee">Non-Coffee</option>
                                <option value="Juice">Juice</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk mengisi modal edit dengan data yang sesuai
        var editMenuModal = document.getElementById('editMenuModal');
        editMenuModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var desc = button.getAttribute('data-desc');
            var price = button.getAttribute('data-price');
            var category = button.getAttribute('data-category');
            
            var modalTitle = editMenuModal.querySelector('.modal-title');
            var menuId = editMenuModal.querySelector('#edit_menu_item_id');
            var menuName = editMenuModal.querySelector('#edit_item_name');
            var menuDesc = editMenuModal.querySelector('#edit_description');
            var menuPrice = editMenuModal.querySelector('#edit_price');
            var menuCategory = editMenuModal.querySelector('#edit_category');
            
            modalTitle.textContent = 'Edit Menu: ' + name;
            menuId.value = id;
            menuName.value = name;
            menuDesc.value = desc;
            menuPrice.value = price;
            menuCategory.value = category;
        });
    </script>
</body>
</html>