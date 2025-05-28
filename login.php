<?php
require_once 'function.php';
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session and redirect
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    header('Location: index.php');
                    exit();
                } else {
                    $errors[] = 'Invalid email or password';
                }
            } else {
                $errors[] = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
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
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
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