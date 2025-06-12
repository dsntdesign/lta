<?php
require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$success = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    require 'vendor/autoload.php';
    $file = $_FILES['excel_file']['tmp_name'];
    if (!$file) {
        $errors[] = "File tidak ditemukan.";
    } else {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            for ($i = 2; $i <= count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row['A'])) continue; // skip jika kode_toko kosong

                // Ambil ID relasi dari nama
                $wilayah_id = getIdByName($conn, 'wilayah_perizinan', $row['C'], 'nama_wilayah');
                $kategori_id = getIdByName($conn, 'kategori_perizinan', $row['D'], 'nama_kategori');
                $pemohon_id = getIdByName($conn, 'users', $row['E'], 'nama_lengkap');

                if (!$wilayah_id || !$kategori_id || !$pemohon_id) {
                    $errors[] = "Baris $i: Data master tidak ditemukan (wilayah, kategori, pemohon).";
                    continue;
                }

                $sql = "INSERT INTO dokumen_perizinan 
                    (kode_toko, nama_toko, wilayah_id, kategori_id, pemohon_id, tanggal_pengajuan, tanggal_terbit, tanggal_berlaku_sampai, status, catatan, created_by, updated_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $created_by = $_SESSION['user_id'];
                $updated_by = $_SESSION['user_id'];
                $stmt->bind_param(
                    "ssiiisssssis",
                    $row['A'], // kode_toko
                    $row['B'], // nama_toko
                    $wilayah_id,
                    $kategori_id,
                    $pemohon_id,
                    $row['F'], // tanggal_pengajuan
                    $row['G'], // tanggal_terbit
                    $row['H'], // tanggal_berlaku_sampai
                    $row['I'], // status
                    $row['J'], // catatan
                    $created_by,
                    $updated_by
                );
                if ($stmt->execute()) {
                    $success[] = "Baris $i: Berhasil diimport.";
                } else {
                    $errors[] = "Baris $i: Gagal import. " . $conn->error;
                }
            }
        } catch (Exception $e) {
            $errors[] = "File tidak valid atau rusak: " . $e->getMessage();
        }
    }
}

// Helper: get ID by name
function getIdByName($conn, $table, $name, $field = 'nama') {
    $sql = "SELECT id FROM $table WHERE $field = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['id'] : null;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Import Dokumen - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'partials/header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'partials/sidebar.php'; // Tambahkan sidebar di sini ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="container mt-4">
                <h2>Import Data Dokumen dari Excel</h2>
                <a href="assets/template_import_dokumens.xlsx" class="btn btn-success mb-3">
                    <i class="bi bi-download"></i> Download Template Excel
                </a>
                <form method="post" enctype="multipart/form-data" class="mb-4">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Pilih File Excel (.xlsx)</label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= implode('<br>', $success) ?></div>
                <?php endif; ?>
                <?php if ($errors): ?>
                    <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
                <?php endif; ?>
                <div class="alert alert-info">
                    <strong>Format Kolom Excel:</strong><br>
                    kode_toko, nama_toko, wilayah, kategori, pemohon, tanggal_pengajuan, tanggal_terbit, tanggal_berlaku_sampai, status, catatan<br>
                    <small>Pastikan semua data master (wilayah, kategori, pemohon) sudah ada sebelum import.</small>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>