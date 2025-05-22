<?php
require_once 'config.php';
require_once 'functions/functions.php';

// Ambil dokumen yang akan kedaluwarsa dalam 7 hari
$today = new DateTime();
$seven_days_later = $today->modify('+7 days')->format('Y-m-d');

// Query untuk dokumen yang akan kedaluwarsa dalam 7 hari
$sql_upcoming = "SELECT dp.*, u.email AS pemohon_email, u.nama_lengkap AS pemohon, wp.nama_wilayah, kp.nama_kategori
                 FROM dokumen_perizinan dp
                 LEFT JOIN users u ON dp.pemohon_id = u.id
                 LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
                 LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
                 WHERE dp.tanggal_berlaku_sampai = ?";
$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param("s", $seven_days_later);
$stmt_upcoming->execute();
$result_upcoming = $stmt_upcoming->get_result();

// Query untuk dokumen yang sudah kedaluwarsa
$sql_expired = "SELECT dp.*, u.email AS pemohon_email, u.nama_lengkap AS pemohon, wp.nama_wilayah, kp.nama_kategori
                FROM dokumen_perizinan dp
                LEFT JOIN users u ON dp.pemohon_id = u.id
                LEFT JOIN wilayah_perizinan wp ON dp.wilayah_id = wp.id
                LEFT JOIN kategori_perizinan kp ON dp.kategori_id = kp.id
                WHERE dp.tanggal_berlaku_sampai < ?";
$stmt_expired = $conn->prepare($sql_expired);
$stmt_expired->bind_param("s", $seven_days_later);
$stmt_expired->execute();
$result_expired = $stmt_expired->get_result();

// Fungsi untuk mengirim email
require 'vendor/autoload.php'; // Pastikan PHPMailer sudah diinstal

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {

        // Debugging
        //$mail->SMTPDebug = 2; // Aktifkan debugging
        //$mail->Debugoutput = 'html';

        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Server SMTP Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'dwi.susanto@lta.co.id'; // Alamat email Gmail Anda
        $mail->Password = 'ghth wczv pprp otex'; // Password Gmail Anda Mail
        //$mail->Password = 'cirh vhph styh ypvd'; // Password Gmail Anda Other
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        //$mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Pengaturan email
        $mail->setFrom('dwi.susanto@lta.co.id', 'Admin DMS');
        $mail->addAddress($to); // Email penerima
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Kirim email
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Gagal mengirim email. Error: {$mail->ErrorInfo}";
        return false;
    }
}

// Tampilkan tabel
echo "<h2>Pemberitahuan Email Kedaluwarsa</h2>";
echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<thead>
        <tr>
            <th>No</th>
            <th>Keterangan</th>
            <th>Tanggal/Waktu Email Terkirim</th>
        </tr>
      </thead>";
echo "<tbody>";

$no = 1;

// Kirim email untuk dokumen yang akan kedaluwarsa
if ($result_upcoming->num_rows > 0) {
    while ($row = $result_upcoming->fetch_assoc()) {
        $pemohon = isset($row['pemohon']) ? $row['pemohon'] : 'Tidak diketahui';
        $nama_wilayah = isset($row['nama_wilayah']) ? $row['nama_wilayah'] : 'Tidak diketahui';
        $nama_kategori = isset($row['nama_kategori']) ? $row['nama_kategori'] : 'Tidak diketahui';

        $to = $row['pemohon_email'];
        $subject = "Reminder: Dokumen Akan Kedaluwarsa";
        $message = "
            <p>Yth. {$pemohon},</p>
            <p>Dokumen berikut akan kedaluwarsa dalam 7 hari:</p>
            <ul>
                <li><b>Kode Toko:</b> {$row['kode_toko']}</li>
                <li><b>Nama Toko:</b> {$row['nama_toko']}</li>
                <li><b>Wilayah:</b> {$nama_wilayah}</li>
                <li><b>Item Izin:</b> {$nama_kategori}</li>
                <li><b>Tanggal Berlaku Sampai:</b> {$row['tanggal_berlaku_sampai']}</li>
            </ul>
            <p>Segera lakukan perpanjangan dokumen jika diperlukan.</p>
            <p>Salam,</p>
            <p>Tim DMS Perizinan</p>
        ";

        if (sendEmail($to, $subject, $message)) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>Email reminder untuk dokumen yang akan kedaluwarsa dikirim ke {$to}</td>
                    <td>" . date('Y-m-d H:i:s') . "</td>
                  </tr>";
            $no++;
        }
    }
}

// Kirim email untuk dokumen yang sudah kedaluwarsa
if ($result_expired->num_rows > 0) {
    while ($row = $result_expired->fetch_assoc()) {
        $pemohon = isset($row['pemohon']) ? $row['pemohon'] : 'Tidak diketahui';
        $nama_wilayah = isset($row['nama_wilayah']) ? $row['nama_wilayah'] : 'Tidak diketahui';
        $nama_kategori = isset($row['nama_kategori']) ? $row['nama_kategori'] : 'Tidak diketahui';

        $to = $row['pemohon_email'];
        $subject = "Reminder: Dokumen Sudah Kedaluwarsa";
        $message = "
            <p>Yth. {$pemohon},</p>
            <p>Dokumen berikut sudah kedaluwarsa:</p>
            <ul>
                <li><b>Kode Toko:</b> {$row['kode_toko']}</li>
                <li><b>Nama Toko:</b> {$row['nama_toko']}</li>
                <li><b>Wilayah:</b> {$nama_wilayah}</li>
                <li><b>Item Izin:</b> {$nama_kategori}</li>
                <li><b>Tanggal Berlaku Sampai:</b> {$row['tanggal_berlaku_sampai']}</li>
            </ul>
            <p>Segera lakukan perpanjangan dokumen jika diperlukan.</p>
            <p>Salam,</p>
            <p>Tim DMS Perizinan</p>
        ";

        if (sendEmail($to, $subject, $message)) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>Email reminder untuk dokumen yang sudah kedaluwarsa dikirim ke {$to}</td>
                    <td>" . date('Y-m-d H:i:s') . "</td>
                  </tr>";
            $no++;
        }
    }
}

echo "</tbody>";
echo "</table>";
?>