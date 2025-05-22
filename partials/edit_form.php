<div class="mb-3">
    <label for="kode_toko" class="form-label">Kode Toko</label>
    <input type="text" class="form-control" id="kode_toko" name="kode_toko" value="<?= htmlspecialchars($dokumen['kode_toko']) ?>" required>
</div>
<div class="mb-3">
    <label for="nama_toko" class="form-label">Nama Toko</label>
    <input type="text" class="form-control" id="nama_toko" name="nama_toko" value="<?= htmlspecialchars($dokumen['nama_toko']) ?>" required>
</div>
<div class="mb-3">
    <label for="wilayah_id" class="form-label">Wilayah</label>
    <select class="form-select" id="wilayah_id" name="wilayah_id" required>
        <option value="">Pilih Wilayah</option>
        <?php while ($row = $result_wilayah->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $dokumen['wilayah_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['nama_wilayah']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>
<div class="mb-3">
    <label for="kategori_id" class="form-label">Kategori</label>
    <select class="form-select" id="kategori_id" name="kategori_id" required>
        <option value="">Pilih Kategori</option>
        <?php while ($row = $result_kategori->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $dokumen['kategori_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['nama_kategori']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>
<div class="mb-3">
    <label for="pemohon_id" class="form-label">Pemohon</label>
    <select class="form-select" id="pemohon_id" name="pemohon_id" required>
        <option value="">Pilih Pemohon</option>
        <?php while ($row = $result_pemohon->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $dokumen['pemohon_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['nama_lengkap']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>
<div class="mb-3">
    <label for="tanggal_pengajuan" class="form-label">Tanggal Input</label>
    <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="<?= htmlspecialchars($dokumen['tanggal_pengajuan']) ?>" required>
</div>
<div class="mb-3">
    <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
    <input type="date" class="form-control" id="tanggal_terbit" name="tanggal_terbit" value="<?= htmlspecialchars($dokumen['tanggal_terbit']) ?>">
</div>
<div class="mb-3">
    <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku Sampai</label>
    <input type="date" class="form-control" id="tanggal_berlaku" name="tanggal_berlaku" value="<?= htmlspecialchars($dokumen['tanggal_berlaku_sampai']) ?>">
</div>
<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select class="form-select" id="status" name="status" required>
        <option value="Approved" <?= $dokumen['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
    </select>
</div>
<div class="mb-3">
    <label for="file_dokumen" class="form-label">File Dokumen</label>
    <input type="file" class="form-control" id="file_dokumen" name="file_dokumen">
    <?php if (!empty($dokumen['file_path'])): ?>
        <small class="text-muted">File saat ini: <a href="<?= htmlspecialchars($dokumen['file_path']) ?>" target="_blank">Lihat File</a></small>
    <?php endif; ?>
</div>
<div class="mb-3">
    <label for="catatan" class="form-label">Catatan</label>
    <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= htmlspecialchars($dokumen['catatan']) ?></textarea>
</div>