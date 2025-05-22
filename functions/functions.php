<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// ==========================
// Fungsi untuk Dokumen
// ==========================

// Ambil semua dokumen
function getAllDocuments($conn) {
    $sql = "SELECT dp.*, kp.nama_kategori, u.nama_lengkap AS pemohon, wp.nama_wilayah
            FROM dokumen_perizinan dp
            LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
            LEFT JOIN users u ON dp.pemohon_id = u.id
            LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
            WHERE dp.status != 'draft'
            ORDER BY dp.created_by DESC";
    return $conn->query($sql);
}

// Ambil dokumen berdasarkan ID
function getDocumentById($conn, $dokumen_id) {
    $sql = "SELECT d.*, w.nama_wilayah, k.nama_kategori, p.nama_lengkap AS pemohon 
            FROM dokumen_perizinan d
            LEFT JOIN kategori_perizinan k ON d.kategori_id = k.id
            LEFT JOIN users p ON d.pemohon_id = p.id
            LEFT JOIN wilayah_perizinan w ON d.wilayah_id = w.id
            WHERE d.status != 'draft' AND d.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dokumen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Ambil data lampiran dokumen
function getLampiranByDokumenId($conn, $dokumen_id) {
    $sql = "SELECT * FROM lampiran_dokumen WHERE dokumen_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dokumen_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Hitung jumlah dokumen berdasarkan status (Statistik)
$count_aktif = $conn->query("SELECT COUNT(*) AS total FROM dokumen_perizinan WHERE tanggal_berlaku_sampai >= CURDATE()")->fetch_assoc()['total'];
$count_akan_kedaluwarsa = $conn->query("SELECT COUNT(*) AS total FROM dokumen_perizinan WHERE tanggal_berlaku_sampai >= CURDATE() AND DATEDIFF(tanggal_berlaku_sampai, CURDATE()) <= 90")->fetch_assoc()['total'];
$count_kedaluwarsa = $conn->query("SELECT COUNT(*) AS total FROM dokumen_perizinan WHERE tanggal_berlaku_sampai < CURDATE()")->fetch_assoc()['total'];

//Mengambil Berdasarkan Range tanggal
function getDocumentsByDateRange($conn, $start_date, $end_date) {
    $sql = "SELECT dp.*, wp.nama_wilayah, kp.nama_kategori, u.nama_lengkap AS pemohon
            FROM dokumen_perizinan dp
            LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
            LEFT JOIN users u ON dp.pemohon_id = u.id
            LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
            WHERE dp.tanggal_pengajuan BETWEEN ? AND ?
            ORDER BY dp.tanggal_pengajuan ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


// ==========================
// Fungsi untuk Wilayah
// ==========================

// Ambil semua wilayah
function getAllWilayah($conn) {
    $sql = "SELECT * FROM wilayah_perizinan ORDER BY created_at DESC";
    return $conn->query($sql);
}

// Ambil daftar pemohon
function getAllPemohon($conn) {
    $sql = "SELECT id, nama_lengkap FROM users ORDER BY nama_lengkap ASC";
    return $conn->query($sql);
}

// ==========================
// Fungsi untuk Kategori
// ==========================

// Ambil semua kategori
function getAllKategori($conn) {
    $sql = "SELECT * FROM kategori_perizinan ORDER BY created_at DESC";
    return $conn->query($sql);
}

// ==========================
// Fungsi untuk Pencarian Dokumen
// ==========================

function searchDocuments($conn, $search, $status_filter) {
    $sql = "SELECT dp.*, wp.nama_wilayah, kp.nama_kategori, u.nama_lengkap AS pemohon, 
            DATEDIFF(dp.tanggal_berlaku_sampai, CURDATE()) AS sisa_hari
            FROM dokumen_perizinan dp
            LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
            LEFT JOIN users u ON dp.pemohon_id = u.id
            LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
            WHERE (dp.kode_toko LIKE ? OR dp.nama_toko LIKE ?)";

    $params = ["%$search%", "%$search%"];

    if ($status_filter === 'kedaluwarsa') {
        $sql .= " AND dp.tanggal_berlaku_sampai < CURDATE()";
    } elseif ($status_filter === 'akan_tujuh_hari') {
        $sql .= " AND dp.tanggal_berlaku_sampai >= CURDATE() AND DATEDIFF(dp.tanggal_berlaku_sampai, CURDATE()) BETWEEN 0 AND 7";
    } elseif ($status_filter === 'akan_sembilanpuluh_hari') {
        $sql .= " AND dp.tanggal_berlaku_sampai >= CURDATE() AND DATEDIFF(dp.tanggal_berlaku_sampai, CURDATE()) BETWEEN 8 AND 90";
    }

    $sql .= " ORDER BY dp.tanggal_berlaku_sampai ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

// ==========================
// Fungsi untuk Edit Dokumen
// ==========================

// Ambil data dokumen untuk diedit
function getEditDokumen($conn, $dokumen_id) {
    $sql_dokumen = "SELECT * FROM dokumen_perizinan WHERE id = ?";
    $stmt_dokumen = $conn->prepare($sql_dokumen);
    $stmt_dokumen->bind_param("i", $dokumen_id);
    $stmt_dokumen->execute();
    $result_dokumen = $stmt_dokumen->get_result();
    return $result_dokumen->fetch_assoc();
}

// ==========================
// Fungsi untuk Validasi
// ==========================

// Validasi parameter ID dokumen
function validateDokumenId($dokumen_id) {
    if (!isset($dokumen_id) || empty($dokumen_id)) {
        die("ID dokumen tidak ditemukan.");
    }
    return intval($dokumen_id);
}

//Ambil data users
function getAllUsers($conn) {
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    return $conn->query($sql);
}

//Pagination
function getDocumentsWithPagination($conn, $offset, $limit) {
    $sql = "SELECT dp.*, wp.nama_wilayah, kp.nama_kategori, u.nama_lengkap AS pemohon
            FROM dokumen_perizinan dp
            LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
            LEFT JOIN users u ON dp.pemohon_id = u.id
            LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
            ORDER BY dp.tanggal_pengajuan DESC
            LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk menghitung total dokumen statistik
function getTotalDocuments($conn) {
    $sql = "SELECT COUNT(*) AS total FROM dokumen_perizinan";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Fungsi untuk mendapatkan statistik jumlah dokumen berdasarkan wilayah
function getWilayahStatistics($conn) {
    $sql = "SELECT wp.nama_wilayah, COUNT(dp.id) AS jumlah
            FROM dokumen_perizinan dp
            LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
            GROUP BY wp.nama_wilayah
            ORDER BY jumlah DESC";
    $result = $conn->query($sql);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Fungsi untuk mendapatkan statistik jumlah dokumen berdasarkan kategori item izin
function getItemIzinStatistics($conn) {
    $sql = "SELECT kp.nama_kategori, COUNT(dp.id) AS jumlah
            FROM dokumen_perizinan dp
            LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
            GROUP BY kp.nama_kategori
            ORDER BY jumlah DESC";
    $result = $conn->query($sql);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}    
?>