<?php
session_start();
// --- 1. CEK LOGIN & KONEKSI ---
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:../index.php");
    exit();
}
include '../config/database.php';

// --- 2. LOGIKA FILTER TANGGAL (Untuk Laporan & Tabel) ---
if(isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])){
    $tgl_awal = $_GET['tgl_awal'];
    $tgl_akhir = $_GET['tgl_akhir'];
} else {
    // Default: Tanggal 1 bulan ini s/d Hari ini
    $tgl_awal = date('Y-m-01');
    $tgl_akhir = date('Y-m-d');
}

// --- 3. LOGIKA HITUNG SALDO (Berdasarkan Filter) ---
// Total Pemasukan
$q_masuk = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transactions WHERE tipe='pemasukan' AND (tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir')");
$d_masuk = mysqli_fetch_assoc($q_masuk); 
$total_masuk = $d_masuk['total'] ?: 0;

// Total Pengeluaran
$q_keluar = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transactions WHERE tipe='pengeluaran' AND (tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir')");
$d_keluar = mysqli_fetch_assoc($q_keluar); 
$total_keluar = $d_keluar['total'] ?: 0;

// Saldo Akhir
$saldo = $total_masuk - $total_keluar;

// --- 4. DATA RIWAYAT TRANSAKSI (Tabel) ---
$q_history = mysqli_query($conn, "
    SELECT t.*, u.nama as nama_user 
    FROM transactions t 
    JOIN users u ON t.user_id = u.id 
    WHERE (t.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir')
    ORDER BY t.tanggal DESC, t.id DESC 
");

// --- 5. DATA KATEGORI (Untuk Dropdown Form) ---
$q_kategori = mysqli_query($conn, "SELECT * FROM categories ORDER BY nama_kategori ASC");
$list_kategori = [];
while($k = mysqli_fetch_assoc($q_kategori)){ $list_kategori[] = $k; }

// --- 6. LOGIKA BUDGETING (Realtime Bulan Ini) ---
// Fitur ini menghitung pemakaian Bulan Ini, tidak terpengaruh filter tanggal laporan
$bulan_ini = date('m');
$tahun_ini = date('Y');
$q_budget = mysqli_query($conn, "SELECT * FROM categories WHERE tipe='pengeluaran' AND batas_anggaran > 0");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard FamilyFin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4 no-print">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#"><i class="fas fa-wallet"></i> FamilyFin</a>
    <div class="d-flex align-items-center text-white">
        <span class="me-3"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['nama']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)</span>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h3>Dashboard Keuangan</h3>
        <div>
            <a href="savings.php" class="btn btn-warning text-dark fw-bold me-2 shadow-sm">
                <i class="fas fa-piggy-bank"></i> Celengan Impian
            </a>

            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print"></i> Cetak
            </button>

            <?php if($_SESSION['role'] != 'anak'): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus"></i> Catat Transaksi
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4 no-print shadow-sm">
        <div class="card-body py-2">
            <form action="" method="GET" class="row g-2 align-items-center">
                <div class="col-auto"><label class="fw-bold">Periode:</label></div>
                <div class="col-auto"><input type="date" name="tgl_awal" class="form-control form-control-sm" value="<?php echo $tgl_awal; ?>" required></div>
                <div class="col-auto"><span>s/d</span></div>
                <div class="col-auto"><input type="date" name="tgl_akhir" class="form-control form-control-sm" value="<?php echo $tgl_akhir; ?>" required></div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Tampilkan</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if($_SESSION['role'] == 'ayah'): ?>
    <div class="card bg-dark text-white mb-4 no-print shadow">
        <div class="card-body d-flex justify-content-between align-items-center p-3">
            <div>
                <h5 class="mb-0"><i class="fas fa-cogs"></i> Panel Admin Keluarga</h5>
                <small class="text-white-50">Kelola akun user, kategori, dan batas anggaran.</small>
            </div>
            <div>
                <a href="users.php" class="btn btn-warning text-dark fw-bold me-2"><i class="fas fa-users"></i> User</a>
                <a href="categories.php" class="btn btn-info text-dark fw-bold"><i class="fas fa-tags"></i> Kategori & Budget</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-saldo p-3 mb-2">
                <h6>Saldo (Periode Ini)</h6>
                <h2 class="fw-bold">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 mb-2 border-start border-success border-5">
                <h6 class="text-success">Pemasukan</h6>
                <h4>Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 mb-2 border-start border-danger border-5">
                <h6 class="text-danger">Pengeluaran</h6>
                <h4>Rp <?php echo number_format($total_keluar, 0, ',', '.'); ?></h4>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3"><i class="fas fa-tachometer-alt"></i> Monitoring Anggaran (Bulan Ini)</div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        if(mysqli_num_rows($q_budget) == 0) echo "<div class='text-center text-muted py-2'>Belum ada target anggaran.</div>";

                        while($b = mysqli_fetch_assoc($q_budget)): 
                            $kat = $b['nama_kategori'];
                            $q_pakai = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transactions WHERE kategori='$kat' AND tipe='pengeluaran' AND MONTH(tanggal) = '$bulan_ini' AND YEAR(tanggal) = '$tahun_ini'");
                            $d_pakai = mysqli_fetch_assoc($q_pakai);
                            $terpakai = $d_pakai['total'] ?: 0;
                            
                            $persen = ($b['batas_anggaran'] > 0) ? ($terpakai / $b['batas_anggaran']) * 100 : 0;
                            
                            // Logika Warna Bar
                            $warna_bar = "bg-success"; 
                            if($persen >= 75) $warna_bar = "bg-warning"; 
                            if($persen >= 100) $warna_bar = "bg-danger";
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold"><?php echo $kat; ?></span>
                                <small class="<?php echo ($persen >= 100) ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                    Rp <?php echo number_format($terpakai,0,',','.'); ?> / Rp <?php echo number_format($b['batas_anggaran'],0,',','.'); ?>
                                </small>
                            </div>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar <?php echo $warna_bar; ?>" role="progressbar" style="width: <?php echo ($persen > 100) ? 100 : $persen; ?>%">
                                    <?php if($persen >= 100) echo "OVER!"; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3">
                    <i class="fas fa-history"></i> Riwayat Transaksi
                    <span class="badge bg-light text-dark ms-2 fw-normal">
                        <?php echo date('d M', strtotime($tgl_awal)) . ' - ' . date('d M Y', strtotime($tgl_akhir)); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th><th>Oleh</th><th>Kategori</th><th>Jumlah</th><th class="no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($q_history) == 0): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                                <?php endif; ?>

                                <?php while($row = mysqli_fetch_assoc($q_history)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td><small class="badge bg-secondary"><?php echo $row['nama_user']; ?></small></td>
                                    <td><?php echo $row['kategori']; ?></td>
                                    <td class="<?php echo ($row['tipe'] == 'pemasukan') ? 'text-success fw-bold' : 'text-danger fw-bold'; ?>">
                                        <?php echo ($row['tipe'] == 'pemasukan') ? '+' : '-'; ?> 
                                        Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="no-print">
                                        <?php if($_SESSION['role'] != 'anak'): ?>
                                            <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id']; ?>"><i class="fas fa-edit"></i></button>
                                            
                                            <a href="../actions/delete_trans.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="konfirmasiHapus(event)">
                                               <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="fas fa-lock"></i> Locked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <?php if($_SESSION['role'] != 'anak'): ?>
                                <div class="modal fade" id="modalEdit<?php echo $row['id']; ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-white"><h5 class="modal-title">Edit</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><form action="../actions/edit_trans.php" method="POST"><div class="modal-body"><input type="hidden" name="id_transaksi" value="<?php echo $row['id']; ?>"><div class="mb-3"><label>Jenis</label><select name="tipe" class="form-select"><option value="pemasukan" <?php if($row['tipe']=='pemasukan') echo 'selected'; ?>>Pemasukan</option><option value="pengeluaran" <?php if($row['tipe']=='pengeluaran') echo 'selected'; ?>>Pengeluaran</option></select></div><div class="mb-3"><label>Kategori</label><select name="kategori" class="form-select"><option value="<?php echo $row['kategori']; ?>"><?php echo $row['kategori']; ?> (Saat Ini)</option><?php foreach($list_kategori as $kat): ?><option value="<?php echo $kat['nama_kategori']; ?>"><?php echo $kat['nama_kategori']; ?></option><?php endforeach; ?></select></div><div class="mb-3"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" value="<?php echo $row['jumlah']; ?>" required></div><div class="mb-3"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?php echo $row['tanggal']; ?>" required></div><div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"><?php echo $row['keterangan']; ?></textarea></div></div><div class="modal-footer"><button type="submit" name="update_transaksi" class="btn btn-warning">Update</button></div></form></div></div></div>
                                <?php endif; ?>

                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3"><i class="fas fa-chart-pie"></i> Analisa</div>
                <div class="card-body text-center"><canvas id="financeChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<?php if($_SESSION['role'] != 'anak'): ?>
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white"><h5 class="modal-title">Catat Baru</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <form action="../actions/add_trans.php" method="POST">
      <div class="modal-body">
        <div class="mb-3"><label>Jenis</label><select name="tipe" class="form-select"><option value="pengeluaran">Pengeluaran</option><option value="pemasukan">Pemasukan</option></select></div>
        <div class="mb-3"><label>Kategori</label><select name="kategori" class="form-select" required><option value="">-- Pilih --</option><?php foreach($list_kategori as $kat): ?><option value="<?php echo $kat['nama_kategori']; ?>"><?php echo $kat['nama_kategori']; ?></option><?php endforeach; ?></select>
            <?php if($_SESSION['role']=='ayah'): ?><small><a href="categories.php">+ Atur Kategori</a></small><?php endif; ?>
        </div>
        <div class="mb-3"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
        <div class="mb-3"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" required></div>
        <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"></textarea></div>
      </div>
      <div class="modal-footer"><button type="submit" name="simpan_transaksi" class="btn btn-primary">Simpan</button></div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="../assets/js/script.js"></script>

<script>
    // Config Chart (Tetap disini karena butuh variabel PHP)
    const ctx = document.getElementById('financeChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                data: [<?php echo $total_masuk; ?>, <?php echo $total_keluar; ?>],
                backgroundColor: ['#198754', '#dc3545'], borderWidth: 1
            }]
        }, options: { responsive: true }
    });
</script>

</body>
</html>