<?php
// File: config.php
// Konfigurasi koneksi database
$host = "localhost";
$username = "root";
$password = "";
$database = "dms_perizinan";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=dms_perizinan", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>