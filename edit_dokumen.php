<?php
// filepath: d:\laragon\www\DMS-V2\edit_dokumen.php
require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Ambil ID dokumen dari parameter URL
$dokumen_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data dokumen untuk diedit
$dokumen = getEditDokumen($conn, $dokumen_id);
if (!$dokumen) {
    die("Dokumen tidak ditemukan.");
}

// Ambil daftar kategori, wilayah, dan pemohon
$kategori = getAllKategori($conn);
$wilayah = getAllWilayah($conn);
$pemohon = getAllPemohon($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dokumen - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <h1 class="h2">Edit Dokumen</h1>
                </div>

                <form action="process_edit_dokumen.php?id=<?= $dokumen_id ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="kode_toko" class="form-label">Kode Toko</label>
                        <input type="text" class="form-control" id="kode_toko" name="kode_toko" value="<?= htmlspecialchars($dokumen['kode_toko']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_toko" class="form-label">Nama Toko</label>
                        <input type="text" class="form-control" id="nama_toko" name="nama_toko" value="<?= htmlspecialchars($dokumen['nama_toko']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="wilayah_id" class="form-label">Wilayah</label>
                        <select class="form-select" id="wilayah_id" name="wilayah_id" required>
                            <option value="">Pilih Wilayah</option>
                            <?php while ($row = $wilayah->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>" <?= $row['id'] == $dokumen['wilayah_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nama_wilayah']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php while ($row = $kategori->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>" <?= $row['id'] == $dokumen['kategori_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nama_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pemohon_id" class="form-label">Pemohon</label>
                        <select class="form-select" id="pemohon_id" name="pemohon_id" required>
                            <option value="">Pilih Pemohon</option>
                            <?php while ($row = $pemohon->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>" <?= $row['id'] == $dokumen['pemohon_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nama_lengkap']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pengajuan" class="form-label">Tanggal Input</label>
                        <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="<?= htmlspecialchars($dokumen['tanggal_pengajuan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                        <input type="date" class="form-control" id="tanggal_terbit" name="tanggal_terbit" value="<?= htmlspecialchars($dokumen['tanggal_terbit']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku Sampai</label>
                        <input type="date" class="form-control" id="tanggal_berlaku" name="tanggal_berlaku" value="<?= htmlspecialchars($dokumen['tanggal_berlaku_sampai']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Approved" <?= $dokumen['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Pending" <?= $dokumen['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Rejected" <?= $dokumen['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file_dokumen" class="form-label">File Dokumen</label>
                        <input type="file" class="form-control" id="file_dokumen" name="file_dokumen">
                        <?php if (!empty($dokumen['file_path'])): ?>
                            <p class="mt-2">File saat ini: <a href="<?= htmlspecialchars($dokumen['file_path']) ?>" target="_blank">Unduh</a></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="lampiran" class="form-label">Lampiran Tambahan</label>
                        <input type="file" class="form-control" id="lampiran" name="lampiran[]" multiple>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= htmlspecialchars($dokumen['catatan']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a class="btn btn-secondary" href="dashboard.php">Kembali ke Dashboard</a>
                </form>
            </main>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>