<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data user berdasarkan email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();

        // --- GANTI BLOK DI BAWAH INI ---
        
        // Verifikasi password
        if (password_verify($password, $user_data['password'])) {
            // JIKA PASSWORD BENAR, SIMPAN DATA PENTING KE SESSION
            
            // Simpan semua data user ke dalam satu session
            $_SESSION['user'] = $user_data;

            // PENTING: Simpan juga role ke session terpisah untuk kemudahan pengecekan
            $_SESSION['role'] = $user_data['role'];

            // Logika redirect yang sudah kita perbaiki sebelumnya
            if ($user_data['role'] == 'admin') {
                header('Location: admin/admin_dashboard.php');
                exit();
            } elseif ($user_data['role'] == 'kasir') {
                header('Location: kasir/orders_admin.php');
                exit();
            } elseif ($user_data['role'] == 'waiters') {
                header('Location: waiters/dashboard_waiters.php');
                exit();
            } elseif ($user_data['role'] == 'kitchen') {
                header('Location: chef/chef_dashboard.php');
                exit();
            } elseif ($user_data['role'] == 'manajer') {
                header('Location: manajer/dashboard_manajer.php');
                exit();
            } else {
                header('Location: menulogin.php');
                exit();
            }
        } else {
            $error = "Email atau Password salah!";
        }
    } else {
        $error = "Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <form class="form-login" method="POST" action="login.php">
            <h3 class="fw-normal text-center">Login</h3>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-floating mb-2">
                <input type="email" class="form-control" name="email" placeholder="E-mail" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                <label>E-mail</label>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <label>Password</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="fa fa-sign-in" aria-hidden="true"></i>
                Login
            </button>
            <p class="mb-2 text-center">Don't have an account? <a class="daftar" href="daftar.php">Register</a></p>
            <p class="text-muted text text-center">&copy;Lamperie 2025</p>
        </form>    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>