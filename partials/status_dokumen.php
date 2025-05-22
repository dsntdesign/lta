<?php
if (isset($dokumen['tanggal_terbit']) && isset($dokumen['tanggal_berlaku_sampai'])) {
    $today = new DateTime();
    $expire_date = new DateTime($dokumen['tanggal_berlaku_sampai']);
    $tanggal_terbit = new DateTime($dokumen['tanggal_terbit']);
    $interval = $today->diff($expire_date);
    $total_interval = $tanggal_terbit->diff($expire_date);

    if ($today > $expire_date) {
        echo '<div class="alert alert-danger">Dokumen sudah kadaluarsa!</div>';
    } elseif ($interval->days <= 90) {
        echo '<div class="alert alert-warning">Dokumen akan kadaluarsa dalam '.$interval->days.' hari!</div>';
    } else {
        echo '<div class="alert alert-success">Dokumen berlaku selama '.
             $total_interval->y.' tahun, '.
             $total_interval->m.' bulan, '.
             $total_interval->d.' hari</div>';
    }
} else {
    echo '<div class="alert alert-secondary">Informasi tanggal tidak tersedia.</div>';
}
?>