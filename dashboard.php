<?php
// filepath: d:\laragon\www\DMS\dashboard.php
require_once 'auth.php';
require_once 'config.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Ambil data dokumen
require_once 'functions/functions.php';
//$result = getAllDocuments($conn);

// Statistik untuk Wilayah
$wilayah_stats = getWilayahStatistics($conn); // Fungsi untuk mendapatkan data statistik wilayah
$wilayah_labels = json_encode(array_column($wilayah_stats, 'nama_wilayah'));
$wilayah_data = json_encode(array_column($wilayah_stats, 'jumlah'));

// Statistik untuk Item Izin
$item_izin_stats = getItemIzinStatistics($conn); // Fungsi untuk mendapatkan data statistik item izin
$item_izin_labels = json_encode(array_column($item_izin_stats, 'nama_kategori'));
$item_izin_data = json_encode(array_column($item_izin_stats, 'jumlah'));

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

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DMS Perizinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #statistikDokumen, #statistikWilayah, #statistikItemIzin {
            width: 100%; /* Grafik akan menyesuaikan lebar kolom */
            height: auto; /* Tinggi grafik akan menyesuaikan */
            max-height: 400px; /* Maksimal tinggi grafik */
        }

        .table-responsive {
            overflow-x: auto; /* Tabel dapat digeser secara horizontal jika terlalu lebar */
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 300px; /* Tinggi tetap untuk grafik */
        }
    </style>
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'partials/sidebar.php'; ?>

            <!-- Konten Utama -->
            <main class="col-12 col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="tambah_dokumen.php" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Dokumen
                        </a>
                    </div>
                </div>

                <!-- Statistik dalam Grafik -->
                <div class="row mb-4">
                    <!-- Statistik Dokumen -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="chart-container">
                            <h5>Statistik Dokumen</h5>
                            <canvas id="statistikDokumen"></canvas>
                        </div>
                    </div>

                    <!-- Statistik Wilayah -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="chart-container">
                            <h5>Statistik Wilayah</h5>
                            <canvas id="statistikWilayah"></canvas>
                        </div>
                    </div>

                    <!-- Statistik Item Izin -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="chart-container">
                            <h5>Statistik Item Izin</h5>
                            <canvas id="statistikItemIzin"></canvas>
                        </div>
                    </div>
                </div>
                <script>
                // Statistik Dokumen (Doughnut Chart)
                const ctxDokumen = document.getElementById('statistikDokumen').getContext('2d');
                const statistikDokumen = new Chart(ctxDokumen, {
                    type: 'doughnut',
                    data: {
                        labels: ['Dokumen Aktif', 'Akan Kedaluwarsa', 'Kedaluwarsa'],
                        datasets: [{
                            label: 'Jumlah Dokumen',
                            data: [<?= $count_aktif ?>, <?= $count_akan_kedaluwarsa ?>, <?= $count_kedaluwarsa ?>],
                            backgroundColor: [
                                'rgba(40, 167, 69, 0.7)',
                                'rgba(255, 193, 7, 0.7)',
                                'rgba(220, 53, 69, 0.7)'
                            ],
                            borderColor: [
                                'rgba(40, 167, 69, 1)',
                                'rgba(255, 193, 7, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });

                // Statistik Wilayah (Bar Chart)
                const ctxWilayah = document.getElementById('statistikWilayah').getContext('2d');
                const statistikWilayah = new Chart(ctxWilayah, {
                    type: 'bar',
                    data: {
                        labels: <?= $wilayah_labels ?>,
                        datasets: [{
                            label: 'Jumlah Dokumen per Wilayah',
                            data: <?= $wilayah_data ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Statistik Item Izin (Pie Chart)
                const ctxItemIzin = document.getElementById('statistikItemIzin').getContext('2d');
                const statistikItemIzin = new Chart(ctxItemIzin, {
                    type: 'pie',
                    data: {
                        labels: <?= $item_izin_labels ?>,
                        datasets: [{
                            label: 'Jumlah Dokumen per Item Izin',
                            data: <?= $item_izin_data ?>,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
                </script>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h4>Dokumen Perizinan Terbaru</h4>
                </div>  
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Kode Toko</th>
                                <th>Nama Toko</th>
                                <th>Wilayah</th>
                                <th>Item Izin</th>
                                <th>Pemohon</th>
                                <th>Tanggal Input</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['kode_toko']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_toko']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_wilayah']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                                        <td><?= htmlspecialchars($row['pemohon']); ?></td>
                                        <td><?= htmlspecialchars($row['tanggal_pengajuan']); ?></td>
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
                                            <a href="detail_dokumen.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_dokumen.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if (hasRole('admin')): ?>
                                            <a href="hapus_dokumen.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
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
            </main>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>