<?php
require_once 'koneksi.php';

$error = ''; // Variabel untuk menampung pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone    = $_POST['phone'];

    // 1. Validasi Password
    if ($password !== $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // 2. Cek apakah email sudah ada (menggunakan prepared statement)
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // 3. Jika semua aman, masukkan data pengguna (tanpa member_id dulu)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt_insert->bind_param("ssss", $name, $email, $hashed_password, $phone);

            if ($stmt_insert->execute()) {
                // 4. Ambil ID dari pengguna yang baru saja dibuat
                $new_user_id = $conn->insert_id;

                // 5. Buat Member ID yang unik berdasarkan ID tersebut
                $tahun = date('Y');
                $member_id = "LP-" . $tahun . "-" . str_pad($new_user_id, 4, '0', STR_PAD_LEFT);

                // 6. Update baris pengguna baru dengan Member ID
                $stmt_update = $conn->prepare("UPDATE users SET member_id = ? WHERE id = ?");
                $stmt_update->bind_param("si", $member_id, $new_user_id);
                $stmt_update->execute();
                $stmt_update->close();

                // 7. Arahkan ke halaman login dengan pesan sukses
                header("Location: login.php?status=sukses_daftar");
                exit();
            } else {
                $error = "Pendaftaran gagal, terjadi kesalahan pada server.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <form class="form-login" method="POST" action="daftar.php" onsubmit="return validateForm()">
            <h3 class="fw-normal text-center">Create Account</h3>
            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="name" placeholder="Full Name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                <label>Full Name</label>
            </div>
            <div class="form-floating mb-2">
                <input type="email" class="form-control" name="email" placeholder="E-mail" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                <label>E-mail</label>
            </div>
            <div class="form-floating mb-2">
                <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                <label>Password</label>
            </div>
            <div class="form-floating mb-2">
                <input id="confirm_password" type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                <label>Confirm Password</label>
            </div>
            <div class="form-floating mb-2">
                <input type="tel" class="form-control" name="phone" placeholder="Phone Number" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                <label>Phone Number (Optional)</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="fa fa-user-plus" aria-hidden="true"></i>
                Register
            </button>
            <p class="mb-2 text-center">Already have an account? <a class="daftar" href="login.php">Login</a></p>
            <p class="text-muted text text-center">&copy;Lamperie 2025</p>
        </form>    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/daftar.js"></script>
</body>
</html>