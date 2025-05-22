<?php
require_once 'config.php';
require_once 'auth.php';

$error = '';
$success = '';

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = "Email wajib diisi.";
    } else {
        try {
            // Periksa apakah email terdaftar
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate token reset password
                $token = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email");
                $stmt->execute([':token' => $token, ':email' => $email]);

                // Kirim email reset password
                $reset_link = "http://localhost/DMS-V2/reset_password.php?token=$token";
                $subject = "Reset Password - DMS Perizinan";
                $message = "Klik link berikut untuk mereset password Anda: $reset_link\n\nLink ini berlaku selama 1 jam.";
                $headers = "From: no-reply@dms-lta.com";

                if (mail($email, $subject, $message, $headers)) {
                    $success = "Link reset password telah dikirim ke email Anda.";
                } else {
                    $error = "Gagal mengirim email. Silakan coba lagi.";
                }
            } else {
                $error = "Email tidak ditemukan.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/office-background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        .forgot-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .forgot-container h2 {
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
        <div class="forgot-container">
            <h2>Lupa Password</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
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