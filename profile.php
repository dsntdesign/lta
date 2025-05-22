<?php
// filepath: d:\laragon\www\DMS-V2\profile.php
require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Ambil data pengguna yang sedang login
$user = getCurrentUser();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if (!empty($nama_lengkap) && !empty($email)) {
        if ($password) {
            $sql = "UPDATE users SET nama_lengkap = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nama_lengkap, $email, $password, $user['id']);
        } else {
            $sql = "UPDATE users SET nama_lengkap = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nama_lengkap, $email, $user['id']);
        }

        if ($stmt->execute()) {
            $success = "Profil berhasil diperbarui.";
            // Perbarui data pengguna di sesi
            $_SESSION['user']['nama_lengkap'] = $nama_lengkap;
            $_SESSION['user']['email'] = $email;
        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
    } else {
        $error = "Nama lengkap dan email tidak boleh kosong.";
    }
}

// Sertakan file HTML
include 'partials/profile_view.php';