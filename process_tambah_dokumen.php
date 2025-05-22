<?php
require_once 'auth.php';
require_once 'config.php';

// Pastikan user sudah login
$user = getCurrentUser();
if (!$user) {
    header("Location: login.php");
    exit;
}

// Proses form penambahan dokumen
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $kode_toko = $_POST['kode_toko'];
    $nama_toko = $_POST['nama_toko'];
    $wilayah_id = $_POST['wilayah_id'];
    $kategori_id = $_POST['kategori_id'];
    $pemohon_id = $_POST['pemohon_id'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $tanggal_terbit = !empty($_POST['tanggal_terbit']) ? $_POST['tanggal_terbit'] : NULL;
    $tanggal_berlaku = !empty($_POST['tanggal_berlaku']) ? $_POST['tanggal_berlaku'] : NULL;
    $status = $_POST['status'];
    $catatan = $_POST['catatan'];
    $created_by = $user['id'];
    $updated_by = $user['id'];

    // Validasi input
    if (empty($kode_toko)) {
        $error = "Kode Toko tidak boleh kosong.";
    }
    if (empty($wilayah_id)) {
        $error = "Wilayah tidak boleh kosong.";
    }

    // Upload file jika ada
    $file_path = NULL;
    if (isset($_FILES['file_dokumen']) && $_FILES['file_dokumen']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES["file_dokumen"]["name"]);
        $target_file = $target_dir . $file_name;

        $allowed_ext = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png');
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $error = "Hanya file PDF, DOC, DOCX, JPG, JPEG, dan PNG yang diizinkan.";
        } else if ($_FILES["file_dokumen"]["size"] > 5000000) { // 5MB limit
            $error = "Ukuran file terlalu besar. Maksimal 5MB.";
        } else if (move_uploaded_file($_FILES["file_dokumen"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        } else {
            $error = "Gagal mengunggah file.";
        }
    }

    // Jika tidak ada error, simpan data dokumen
    if (empty($error)) {
        $sql = "INSERT INTO dokumen_perizinan (kode_toko, nama_toko, wilayah_id, kategori_id, pemohon_id, tanggal_pengajuan, 
                tanggal_terbit, tanggal_berlaku_sampai, status, file_path, catatan, created_by, updated_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiiissssssii", 
            $kode_toko, $nama_toko, $wilayah_id, $kategori_id, $pemohon_id, $tanggal_pengajuan, 
            $tanggal_terbit, $tanggal_berlaku, $status, $file_path, $catatan, $created_by, $updated_by);
        
        if ($stmt->execute()) {
            $dokumen_id = $conn->insert_id;

            // Catat histori dokumen
            $sql_histori = "INSERT INTO histori_dokumen (dokumen_id, status_lama, status_baru, catatan, user_id) 
                            VALUES (?, NULL, ?, 'Dokumen baru dibuat', ?)";
            $stmt_histori = $conn->prepare($sql_histori);
            $stmt_histori->bind_param("isi", $dokumen_id, $status, $user['id']);
            $stmt_histori->execute();

            // Tambahkan notifikasi untuk pemohon
            $judul_notif = "Dokumen Baru Dibuat";
            $pesan_notif = "Dokumen dengan kode $kode_toko telah berhasil dibuat dengan status: $status";
            $sql_notif = "INSERT INTO notifikasi (user_id, dokumen_id, judul, pesan) 
                          VALUES (?, ?, ?, ?)";
            $stmt_notif = $conn->prepare($sql_notif);
            $stmt_notif->bind_param("iiss", $pemohon_id, $dokumen_id, $judul_notif, $pesan_notif);
            $stmt_notif->execute();

            $success = "Dokumen berhasil ditambahkan.";
            header("Location: tambah_dokumen.php?id=$dokumen_id&success=1");
            exit;
        } else {
            $error = "Gagal menambahkan dokumen: " . $conn->error;
        }
    }
}
?>