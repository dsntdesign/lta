<?php
// filepath: d:\laragon\www\DMS-V2\detail_dokumen.php
require_once 'auth.php';
require_once 'config.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Ambil data dokumen dan lampiran
require_once 'functions/functions.php';

$dokumen_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$dokumen = getDocumentById($conn, $dokumen_id);

if (!$dokumen) {
    die("Dokumen tidak ditemukan.");
}

// Ambil data lampiran dokumen
$result_lampiran = getLampiranByDokumenId($conn, $dokumen_id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Dokumen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'partials/sidebar.php'; ?>

            <!-- Konten Utama -->
            <main class="col-12 col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Detail Dokumen</h1>
                    <a class="btn btn-secondary" href="dashboard.php">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>

                <!-- Detail Dokumen -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?php include 'partials/status_dokumen.php'; ?></h6>
                    </div>
                    <div class="card-body">
                    <table class="table table-bordered">
                            <tr>
                                <th>Kode Toko</th>
                                <td><?= isset($dokumen['kode_toko']) ? htmlspecialchars($dokumen['kode_toko']) : 'Tidak tersedia' ?></td>
                            </tr>
                            <tr>
                                <th>Nama Toko</th>
                                <td><?= htmlspecialchars($dokumen['nama_toko']) ?></td>
                            </tr>
                            <tr>
                                <th>Wilayah</th>
                                <td><?= htmlspecialchars($dokumen['nama_wilayah']) ?></td>
                            </tr>
                            <tr>
                                <th>Item Izin</th>
                                <td><?= htmlspecialchars($dokumen['nama_kategori']) ?></td>
                            </tr>
                            <tr>
                                <th>Pemohon</th>
                                <td><?= htmlspecialchars($dokumen['pemohon']) ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Input</th>
                                <td><?= htmlspecialchars($dokumen['tanggal_pengajuan']) ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Terbit</th>
                                <td><?= htmlspecialchars($dokumen['tanggal_terbit']) ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Berlaku Sampai</th>
                                <td><?= htmlspecialchars($dokumen['tanggal_berlaku_sampai']) ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php
                                    $today = new DateTime();
                                    $expire_date = new DateTime($dokumen['tanggal_berlaku_sampai']);
                                    $interval = $today->diff($expire_date);

                                    if ($today > $expire_date) {
                                        echo '<span class="badge bg-danger">Kedaluwarsa</span>';
                                    } elseif ($interval->days <= 90) {
                                        echo '<span class="badge bg-warning">Akan Kedaluwarsa</span>';
                                    } else {
                                        echo '<span class="badge bg-success">Aktif</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td><?= nl2br(htmlspecialchars($dokumen['catatan'])) ?></td>
                            </tr>
                            <tr>
                                <th>File Dokumen</th>
                                <td>
                                    <?php if (!empty($dokumen['file_path'])): ?>
                                        <a href="<?= htmlspecialchars($dokumen['file_path']) ?>" target="_blank">Unduh Dokumen</a>
                                    <?php else: ?>
                                        Tidak ada file dokumen.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Lampiran -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lampiran</h6>
                </div>
                <div class="card-body">
                        <?php include 'partials/lampiran.php'; ?>
                </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>