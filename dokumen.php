<?php
// filepath: d:\laragon\www\DMS\dokumen.php
require_once 'auth.php';
require_once 'config.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Ambil data dokumen
require_once 'functions/functions.php';

// Pagination
$limit = 10; // Jumlah dokumen per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max($page, 1); // Pastikan halaman minimal 1
$offset = ($page - 1) * $limit;

// Ambil dokumen dengan pagination
$result = getDocumentsWithPagination($conn, $offset, $limit);

// Hitung total dokumen dan halaman
$total_documents = getTotalDocuments($conn);
$total_pages = ceil($total_documents / $limit);

// Definisikan variabel $search dan $status_filter dengan nilai default
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil data dokumen berdasarkan pencarian dan filter
$result = searchDocuments($conn, $search, $status_filter);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Dokumen - DMS Perizinan</title>
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
                <h1 class="mt-4 mb-4">Daftar Dokumen Perizinan</h1>

            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari dokumen..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="kedaluwarsa" <?= $status_filter === 'kedaluwarsa' ? 'selected' : '' ?>>Kedaluwarsa</option>
                        <option value="akan_tujuh_hari" <?= $status_filter === 'akan_tujuh_hari' ? 'selected' : '' ?>>Akan Kedaluwarsa (7 Hari)</option>
                        <option value="akan_sembilanpuluh_hari" <?= $status_filter === 'akan_sembilanpuluh_hari' ? 'selected' : '' ?>>Akan Kedaluwarsa (90 Hari)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="status-<?= htmlspecialchars($row['status']) ?>">
                                        <td><?= htmlspecialchars($row['kode_toko']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_toko']) ?></td>
                                        <td><?= isset($row['nama_wilayah']) ? htmlspecialchars($row['nama_wilayah']) : 'Tidak ada wilayah' ?></td>
                                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                        <td><?= htmlspecialchars($row['pemohon']) ?></td>
                                        <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                                        <td>
                                            <?php
                                            $today = new DateTime();
                                            $expire_date = new DateTime($row['tanggal_berlaku_sampai']);
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
                                        <td>
                                            <a href="detail_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if (hasRole('admin')): ?>
                                            <a href="hapus_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada dokumen yang ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>   
                <!-- Tombol Kirim Email -->
                <div class="mb-3">
                    <a href="kirim_email.php" class="btn btn-primary">
                        <i class="bi bi-envelope"></i> Kirim Email Reminder
                    </a>
                </div>

            </main>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>