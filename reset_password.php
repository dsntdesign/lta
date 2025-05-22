<?php
require_once 'config.php';
require_once 'auth.php';

$error = '';
$success = '';

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } else {
        try {
            // Periksa token valid
            $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()");
            $stmt->execute([':token' => $token]);
            $user = $stmt->fetch();

            if ($user) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
                $stmt->execute([':password' => $hashed_password, ':id' => $user['id']]);

                $success = "Password berhasil direset. Anda dapat login dengan password baru.";
            } else {
                $error = "Token tidak valid atau telah kedaluwarsa.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    $error = "Token tidak ditemukan.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/office-background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        .reset-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .reset-container h2 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
        .form-control:focus {
            box-shadow: none;
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
        <div class="reset-container">
            <h2>Reset Password</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <a href="login.php" class="btn btn-primary mt-3">Login</a>
            <?php else: ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="mb-3">
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Password Baru" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        © 2025 PT Lintas Tangguh Anugrah. Hak Cipta Dilindungi.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>