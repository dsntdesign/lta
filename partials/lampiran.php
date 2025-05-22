<?php if (isset($result_lampiran) && $result_lampiran->num_rows > 0): ?>
    <ul>
        <?php while ($lampiran = $result_lampiran->fetch_assoc()): ?>
            <li>
                <a href="<?= htmlspecialchars($lampiran['file_path']) ?>" target="_blank">
                    <?= htmlspecialchars($lampiran['nama_file']) ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>Tidak ada lampiran.</p>
<?php endif; ?>