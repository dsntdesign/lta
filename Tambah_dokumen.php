<?php
// filepath: d:\laragon\www\DMS-V2\Tambah_dokumen.php
require_once 'auth.php';
require_once 'config.php';
require_once 'functions/functions.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Ambil data kategori, wilayah, dan pemohon
$kategori = getAllKategori($conn);
$wilayah = getAllWilayah($conn);
$pemohon = getAllPemohon($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dokumen - DMS Perizinan</title>
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tambah Dokumen</h1>
                </div>

                <form action="process_tambah_dokumen.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="kode_toko" class="form-label">Kode Toko</label>
                        <input type="text" class="form-control" id="kode_toko" name="kode_toko" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_toko" class="form-label">Nama Toko</label>
                        <input type="text" class="form-control" id="nama_toko" name="nama_toko" required>
                    </div>
                    <div class="mb-3">
                        <label for="wilayah_id" class="form-label">Wilayah</label>
                        <select class="form-select" id="wilayah_id" name="wilayah_id" required>
                            <option value="">Pilih Wilayah</option>
                            <?php while ($row = $wilayah->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_wilayah']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php while ($row = $kategori->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pemohon_id" class="form-label">Pemohon</label>
                        <select class="form-select" id="pemohon_id" name="pemohon_id" required>
                            <option value="">Pilih Pemohon</option>
                            <?php while ($row = $pemohon->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_lengkap']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pengajuan" class="form-label">Tanggal Input</label>
                        <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                        <input type="date" class="form-control" id="tanggal_terbit" name="tanggal_terbit">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku Sampai</label>
                        <input type="date" class="form-control" id="tanggal_berlaku" name="tanggal_berlaku">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Approved">Approved</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file_dokumen" class="form-label">File Dokumen</label>
                        <input type="file" class="form-control" id="file_dokumen" name="file_dokumen">
                    </div>
                    <div class="mb-3">
                        <label for="lampiran" class="form-label">Lampiran Tambahan</label>
                        <input type="file" class="form-control" id="lampiran" name="lampiran[]" multiple>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a class="btn btn-secondary" href="dashboard.php">Kembali</a>
                </form>
            </main>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>