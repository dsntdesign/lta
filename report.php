<!-- filepath: d:\laragon\www\DMS-V2\report.php -->
<?php
require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Default tanggal
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Ambil data dokumen berdasarkan range tanggal
$documents = [];
if ($start_date && $end_date) {
    $documents = getDocumentsByDateRange($conn, $start_date, $end_date);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Dokumen - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <h1 class="mt-4 mb-4">Report Dokumen</h1>

                <!-- Form Filter Tanggal -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>

                <!-- Tombol Export -->
                <div class="mb-4">
                    <a href="export_excel.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    <a href="export_pdf.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>

                <!-- Tabel Dokumen -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Kode Toko</th>
                                <th>Nama Toko</th>
                                <th>Wilayah</th>
                                <th>Item Izin</th>
                                <th>Pemohon</th>
                                <th>Tgl Input</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($doc['kode_toko']) ?></td>
                                        <td><?= htmlspecialchars($doc['nama_toko']) ?></td>
                                        <td><?= htmlspecialchars($doc['nama_wilayah']) ?></td>
                                        <td><?= htmlspecialchars($doc['nama_kategori']) ?></td>
                                        <td><?= htmlspecialchars($doc['pemohon']) ?></td>
                                        <td><?= htmlspecialchars($doc['tanggal_pengajuan']) ?></td>
                                        <td><?= htmlspecialchars($doc['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada dokumen yang ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>