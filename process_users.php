<?php
// Proses tambah pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && !empty($nama_lengkap)) {
        $sql = "INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $password, $nama_lengkap, $email, $role);

        if ($stmt->execute()) {
            $success = "Pengguna berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan pengguna: " . $conn->error;
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}

// Proses hapus pengguna
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Pengguna berhasil dihapus.";
    } else {
        $error = "Gagal menghapus pengguna: " . $conn->error;
    }
}
?>