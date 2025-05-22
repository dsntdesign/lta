<?php
require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

// Pastikan user admin
if (!hasRole('admin')) {
    header("Location: dashboard.php");
    exit;
}

// Ambil ID dokumen dari parameter GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Ambil data dokumen (untuk hapus file fisik jika ada)
$stmt = $conn->prepare("SELECT file_path FROM dokumen WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($file_path);
$stmt->fetch();
$stmt->close();

// Hapus file fisik jika ada
if (!empty($file_path) && file_exists($file_path)) {
    unlink($file_path);
}

// Hapus data terkait di tabel lain jika ada relasi (contoh: log, lampiran, dsb.)
// Contoh: $conn->query("DELETE FROM lampiran WHERE dokumen_id = $id");

// Hapus data dokumen utama
$stmt = $conn->prepare("DELETE FROM dokumen WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php?msg=deleted");
exit;