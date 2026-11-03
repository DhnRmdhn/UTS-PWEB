<?php

include "koneksi.php";

$sql_ringkasan = "
    SELECT
        COALESCE(SUM(CASE WHEN jenis = 'pemasukan' THEN nominal END), 0) AS total_pemasukan,
        COALESCE(SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal END), 0) AS total_pengeluaran,
        COALESCE(SUM(CASE WHEN jenis = 'pemasukan' THEN nominal END), 0)
        - COALESCE(SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal END), 0) AS saldo
    FROM keuangan
";
$hasil_ringkasan = $conn->query($sql_ringkasan);
$data_ringkasan  = $hasil_ringkasan->fetch_assoc();

$total_pemasukan   = (int) $data_ringkasan['total_pemasukan'];
$total_pengeluaran = (int) $data_ringkasan['total_pengeluaran'];
$saldo             = (int) $data_ringkasan['saldo'];

$sql_riwayat = "SELECT tanggal, jenis, kategori, keterangan, nominal FROM keuangan ORDER BY tanggal DESC, id DESC";
$hasil_riwayat = $conn->query($sql_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%230d6e6e'/%3E%3Ctext x='50' y='68' font-size='60' text-anchor='middle' fill='white'%3E%24%3C/text%3E%3C/svg%3E">
    <title>Ayo Nabung!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="halaman-tabungan">

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark navbar-app">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">Ayo Nabung!</a>
            </div>
        </nav>
    </header>

    <main class="container my-4">

        <?php if (isset($_GET['pesan'])) :
            $tipe = (isset($_GET['tipe']) && $_GET['tipe'] === 'sukses') ? 'success' : 'danger';
        ?>
            <div class="alert alert-<?php echo $tipe; ?> alert-dismissible fade show" role="alert" id="alert-pesan">
                <?php echo htmlspecialchars($_GET['pesan']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <section class="mb-4" aria-label="Ringkasan keuangan">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card summary-card summary-pemasukan h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 card-title text-muted">Total Pemasukan</h2>
                            <p class="card-text fs-4 fw-bold mb-0">
                                Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card summary-pengeluaran h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 card-title text-muted">Total Pengeluaran</h2>
                            <p class="card-text fs-4 fw-bold mb-0">
                                Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card summary-saldo h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 card-title text-muted">Saldo</h2>
                            <p class="card-text fs-4 fw-bold mb-0">
                                Rp <?php echo number_format($saldo, 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4" aria-label="Form tambah transaksi">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="h5 mb-0">Form Tambah/Kurangi Tabungan</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="proses.php" id="form-transaksi" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="tanggal"
                                    name="tanggal"
                                    required
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="" selected disabled>— Pilih jenis —</option>
                                    <option value="pemasukan">Pemasukan</option>
                                    <option value="pengeluaran">Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="" selected disabled>— Pilih jenis dulu —</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="nominal" class="form-label">Nominal (Rp)</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="nominal"
                                    name="nominal"
                                    min="1"
                                    step="1"
                                    placeholder="Minimal 1"
                                    required
                                >
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan (opsional)</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="keterangan"
                                    name="keterangan"
                                    maxlength="100"
                                    placeholder="Contoh: Gaji bulanan, belanja harian"
                                >
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Simpan Tabungan</button>
                            </div>
                        </div>
                        <div id="pesan-validasi" class="text-danger mt-2 small d-none" role="alert"></div>
                    </form>
                </div>
            </div>
        </section>

        <section aria-label="Riwayat transaksi">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="h5 mb-0">Riwayat Tabungan Kamu</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Jenis</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col" class="text-end">Nominal</th>
                            </tr>
                            <tbody>
                                <?php if ($hasil_riwayat && $hasil_riwayat->num_rows > 0) : ?>
                                    <?php while ($baris = $hasil_riwayat->fetch_assoc()) :
                                        // Warna baris/nominal: hijau pemasukan, merah pengeluaran
                                        $kelas_jenis = ($baris['jenis'] === 'pemasukan') ? 'text-success' : 'text-danger';
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($baris['tanggal']); ?></td>
                                            <td class="<?php echo $kelas_jenis; ?>">
                                                <?php echo htmlspecialchars(ucfirst($baris['jenis'])); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($baris['kategori']); ?></td>
                                            <td><?php echo htmlspecialchars($baris['keterangan']); ?></td>
                                            <td class="text-end fw-semibold <?php echo $kelas_jenis; ?>">
                                                Rp <?php echo number_format((int) $baris['nominal'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada tabungan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer py-3 mt-auto">
        <div class="container text-center">
            <p class="mb-0">DHANI RAMADHANI - 252410103033</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>


