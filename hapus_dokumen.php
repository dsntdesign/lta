<?php

require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

if (!hasRole('admin')) {
    header("Location: dashboard.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$conn->begin_transaction();

try {
    // Hapus histori
    $stmt = $conn->prepare("DELETE FROM histori_dokumen WHERE dokumen_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Hapus file lampiran
    $stmt = $conn->prepare("SELECT file_path FROM lampiran_dokumen WHERE dokumen_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($lampiran_path);
    while ($stmt->fetch()) {
        if (!empty($lampiran_path) && file_exists($lampiran_path)) {
            unlink($lampiran_path);
        }
    }
    $stmt->close();

    // Hapus lampiran
    $stmt = $conn->prepare("DELETE FROM lampiran_dokumen WHERE dokumen_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Hapus notifikasi terkait dokumen
    $stmt = $conn->prepare("DELETE FROM notifikasi WHERE dokumen_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // --- Tambahkan penghapusan tabel lain di sini jika ada relasi lain ---

    // Hapus dokumen utama
    $stmt = $conn->prepare("DELETE FROM dokumen_perizinan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header("Location: dashboard.php?msg=deleted");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    // Untuk debug, bisa tampilkan error (jangan di production)
    die("Gagal hapus dokumen: " . $e->getMessage());
}