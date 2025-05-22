<?php
require_once 'auth.php';
require_once 'config.php';

// Pastikan user sudah login
$user = getCurrentUser();
if (!$user) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dokumen_id = intval($_GET['id']);
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
    $updated_by = $user['id'];

    // Validasi input
    if (empty($kode_toko) || empty($wilayah_id)) {
        header("Location: edit_dokumen.php?id=$dokumen_id&error=Input tidak valid");
        exit;
    }

    // Upload file jika ada
    $file_path = $dokumen['file_path'];
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
            header("Location: edit_dokumen.php?id=$dokumen_id&error=Format file tidak valid");
            exit;
        } else if ($_FILES["file_dokumen"]["size"] > 5000000) {
            header("Location: edit_dokumen.php?id=$dokumen_id&error=Ukuran file terlalu besar");
            exit;
        } else if (move_uploaded_file($_FILES["file_dokumen"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        }
    }

    // Update data dokumen
    $sql = "UPDATE dokumen_perizinan 
            SET kode_toko = ?, nama_toko = ?, wilayah_id = ?, kategori_id = ?, pemohon_id = ?, 
                tanggal_pengajuan = ?, tanggal_terbit = ?, tanggal_berlaku_sampai = ?, 
                status = ?, file_path = ?, catatan = ?, updated_by = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiissssssii", 
        $kode_toko, $nama_toko, $wilayah_id, $kategori_id, $pemohon_id, $tanggal_pengajuan, 
        $tanggal_terbit, $tanggal_berlaku, $status, $file_path, $catatan, $updated_by, $dokumen_id);

    if ($stmt->execute()) {
        header("Location: detail_dokumen.php?id=$dokumen_id&success=Dokumen berhasil diperbarui");
        exit;
    } else {
        header("Location: edit_dokumen.php?id=$dokumen_id&error=Gagal memperbarui dokumen");
        exit;
    }
}
?>