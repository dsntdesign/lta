console.log("Dashboard loaded.");

document.addEventListener('DOMContentLoaded', function () {
    console.log("Halaman dimuat.");

    // Countdown untuk tanggal berlaku dokumen
    const tanggalTerbitInput = document.getElementById('tanggal_terbit');
    const tanggalBerlakuInput = document.getElementById('tanggal_berlaku');

    if (tanggalTerbitInput && tanggalBerlakuInput) {
        const countdownDisplay = document.createElement('div');
        countdownDisplay.className = 'mt-2 text-muted';
        tanggalBerlakuInput.parentNode.appendChild(countdownDisplay);

        function updateCountdown() {
            const tanggalTerbit = new Date(tanggalTerbitInput.value);
            const tanggalBerlaku = new Date(tanggalBerlakuInput.value);

            if (!isNaN(tanggalTerbit.getTime()) && !isNaN(tanggalBerlaku.getTime())) {
                const selisihMs = tanggalBerlaku - tanggalTerbit;
                const selisihHari = Math.floor(selisihMs / (1000 * 60 * 60 * 24));

                if (selisihHari > 0) {
                    countdownDisplay.innerHTML = `Dokumen akan berlaku selama: <strong>${selisihHari} hari</strong>`;
                    countdownDisplay.className = 'mt-2 text-success';
                } else if (selisihHari === 0) {
                    countdownDisplay.innerHTML = 'Dokumen akan berakhir hari ini!';
                    countdownDisplay.className = 'mt-2 text-warning';
                } else {
                    countdownDisplay.innerHTML = 'Dokumen sudah kadaluarsa!';
                    countdownDisplay.className = 'mt-2 text-danger';
                }
            } else {
                countdownDisplay.innerHTML = '';
            }
        }

        tanggalTerbitInput.addEventListener('change', updateCountdown);
        tanggalBerlakuInput.addEventListener('change', updateCountdown);
    }

    // Log halaman yang dimuat
    const currentPath = window.location.pathname;
    if (currentPath.includes('detail_dokumen.php')) {
        console.log("Halaman detail dokumen dimuat.");
    } else if (currentPath.includes('edit_dokumen.php')) {
        console.log("Halaman edit dokumen dimuat.");
    } else if (currentPath.includes('dokumen.php')) {
        console.log("Halaman dokumen dimuat.");
    } else if (currentPath.includes('wilayah.php')) {
        console.log("Halaman wilayah dimuat.");
    } else if (currentPath.includes('kategori.php')) {
        console.log("Halaman kategori dimuat.");
    } else if (currentPath.includes('users.php')) {
        console.log("Halaman kategori dimuat.");
    }
});