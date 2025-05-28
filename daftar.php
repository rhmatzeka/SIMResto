<?php
require_once 'function.php';
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);

    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'Email already registered';
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashedPassword, $phone]);
                
                // Get the new user ID
                $userId = $pdo->lastInsertId();
                
                // Set session and redirect
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $name;
                
                header('Location: index.php');
                exit();
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
    <title>Register - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <form class="form-login" method="POST" action="register.php">
            <h3 class="fw-normal text-center">Create Account</h3>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="name" placeholder="Full Name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                <label>Full Name</label>
            </div>
            <div class="form-floating mb-2">
                <input type="email" class="form-control" name="email" placeholder="E-mail" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                <label>E-mail</label>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <label>Password</label>
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
</body>
</html>