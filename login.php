<?php
// File: login.php
require_once 'auth.php';

// Cek jika user sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Proses form login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        if (login($username, $password)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/office-background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.4); /* Ubah alpha untuk tingkat transparansi */
            z-index: 0;
            pointer-events: none;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-container .logo img {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .login-container h2 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #007bff;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
        
        .form-control:focus {
            box-shadow: 1px;
            border: 1px solid #007bff;
        }

        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            width: 100%;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .login-container .options {
            margin-top: 10px;
            font-size: 14px;
        }

        .login-container .options a {
            color: #007bff;
            text-decoration: none;
        }

        .login-container .options a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo">
                <img src="images/logo.png" alt="Logo DMS">
            </div>
            <h2>Login</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <div class="options mt-3">
                    <label>
                        <input type="checkbox" name="remember"> Remember Me
                    </label>
                    <span class="float-end">
                        <a href="forgot_password.php">Lupa password?</a>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <footer>
        © 2025 PT Lintas Tangguh Anugrah. Hak Cipta Dilindungi.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>