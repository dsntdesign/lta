<?php
require_once 'auth.php';
require_once 'config.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Proses tambah kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'];

    if (!empty($nama_kategori)) {
        $sql = "INSERT INTO kategori_perizinan (nama_kategori, deskripsi) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nama_kategori, $deskripsi);

        if ($stmt->execute()) {
            header("Location: kategori.php?success=Kategori berhasil ditambahkan.");
            exit;
        } else {
            header("Location: kategori.php?error=Gagal menambahkan kategori: " . $conn->error);
            exit;
        }
    } else {
        header("Location: kategori.php?error=Nama kategori tidak boleh kosong.");
        exit;
    }
}

// Proses hapus kategori
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM kategori_perizinan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: kategori.php?success=Kategori berhasil dihapus.");
        exit;
    } else {
        header("Location: kategori.php?error=Gagal menghapus kategori: " . $conn->error);
        exit;
    }
}
?>