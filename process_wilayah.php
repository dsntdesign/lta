<?php
require_once 'auth.php';
require_once 'config.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Proses tambah wilayah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_wilayah = $_POST['nama_wilayah'];
    $deskripsi = $_POST['deskripsi'];

    if (!empty($nama_wilayah)) {
        $sql = "INSERT INTO wilayah_perizinan (nama_wilayah, deskripsi) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nama_wilayah, $deskripsi);

        if ($stmt->execute()) {
            header("Location: wilayah.php?success=Wilayah berhasil ditambahkan.");
            exit;
        } else {
            header("Location: wilayah.php?error=Gagal menambahkan wilayah: " . $conn->error);
            exit;
        }
    } else {
        header("Location: wilayah.php?error=Nama wilayah tidak boleh kosong.");
        exit;
    }
}

// Proses hapus wilayah
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM wilayah_perizinan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: wilayah.php?success=Wilayah berhasil dihapus.");
        exit;
    } else {
        header("Location: wilayah.php?error=Gagal menghapus wilayah: " . $conn->error);
        exit;
    }
}
?>