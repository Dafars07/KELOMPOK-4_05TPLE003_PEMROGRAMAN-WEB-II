<?php
session_start();
if($_SESSION['status'] != "login"){ header("location:../index.php"); exit(); }
include '../config/database.php';

// Ambil Data Tabungan
$query_savings = mysqli_query($conn, "SELECT * FROM savings ORDER BY status ASC, id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celengan Impian - FamilyFin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .card-saving { transition: transform 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-saving:hover { transform: translateY(-5px); }
        .bg-tercapai { background-color: #d1e7dd; border: 1px solid #badbcc; }
    </style>
</head>
<body style="background-color: #f0f2f5;">

<nav class="navbar navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php"><i class="fas fa-wallet"></i> FamilyFin</a>
    <div class="d-flex text-white align-items-center">
        <span class="me-3 d-none d-md-block"><i class="fas fa-user"></i> <?php echo $_SESSION['nama']; ?></span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0"><i class="fas fa-piggy-bank text-warning"></i> Celengan Impian</h3>
            <small class="text-muted">Ayo menabung untuk mewujudkan impianmu!</small>
        </div>
        
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBaru">
            <i class="fas fa-plus-circle"></i> Buat Target Baru
        </button>
    </div>

    <div class="row">
        <?php if(mysqli_num_rows($query_savings) == 0): ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/404/404621.png" width="100" style="opacity: 0.5;">
                <p class="text-muted mt-3">Belum ada impian yang dibuat. Yuk buat sekarang!</p>
            </div>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($query_savings)): 
            // 1. Hitung Persentase (Cegah Error Division by Zero)
            $target = $row['target_jumlah'];
            $terkumpul = $row['terkumpul'];
            
            if($target > 0){
                $persen = ($terkumpul / $target) * 100;
            } else {
                $persen = 0;
            }

            // 2. Logika Tampilan Bar
            $width = ($persen > 100) ? 100 : $persen;
            $status_class = ($row['status'] == 'tercapai') ? 'bg-tercapai' : 'bg-white';
            
            // 3. Pesan Semangat
            $badge = ($row['status'] == 'tercapai') 
                ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> TERCAPAI!</span>' 
                : '<span class="badge bg-primary">Sedang Berjalan</span>';
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-saving h-100 <?php echo $status_class; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title fw-bold text-dark"><?php echo htmlspecialchars($row['nama_target']); ?></h5>
                        <?php echo $badge; ?>
                    </div>
                    <p class="text-muted small mb-3 border-bottom pb-2">
                        <i class="fas fa-sticky-note me-1"></i> <?php echo htmlspecialchars($row['keterangan']); ?>
                    </p>

                    <div class="d-flex justify-content-between fw-bold mb-1">
                        <span class="text-success" style="font-size: 1.1rem;">
                            Rp <?php echo number_format($terkumpul, 0, ',', '.'); ?>
                        </span>
                        <span class="text-secondary small align-self-center">
                            / Rp <?php echo number_format($target, 0, ',', '.'); ?>
                        </span>
                    </div>

                    <div class="progress mb-3 shadow-sm" style="height: 25px; border-radius: 15px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning text-dark" 
                             role="progressbar" 
                             style="width: <?php echo $width; ?>%; font-weight: bold;">
                            <?php echo number_format($persen, 0); ?>%
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <?php if($row['status'] == 'aktif'): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTopup<?php echo $row['id']; ?>">
                                <i class="fas fa-coins"></i> Masukkan Uang (Nabung)
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-success" disabled>
                                <i class="fas fa-award"></i> Hebat! Impian Terwujud
                            </button>
                        <?php endif; ?>

                        <?php if($_SESSION['role'] == 'ayah'): ?>
                            <a href="../actions/savings_action.php?hapus_id=<?php echo $row['id']; ?>" 
                               class="btn btn-link text-danger text-decoration-none btn-sm"
                               onclick="return confirm('Yakin hapus celengan ini?');">
                               <i class="fas fa-trash"></i> Hapus Celengan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalTopup<?php echo $row['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h6 class="modal-title">Nabung: <?php echo $row['nama_target']; ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="../actions/savings_action.php" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="id_saving" value="<?php echo $row['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label small">Nominal (Rp)</label>
                                <input type="number" name="jumlah_nabung" class="form-control" placeholder="0" min="1000" required>
                                <div class="form-text text-muted">Ayo sisihkan uang jajanmu!</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="topup_target" class="btn btn-success w-100">Simpan Uang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="modalBaru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-star"></i> Impian Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/savings_action.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Impian</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Beli Sepeda, Liburan, Tas Baru" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Butuh Uang Berapa? (Rp)</label>
                        <input type="number" name="target" class="form-control" placeholder="Contoh: 500000" min="1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Semangat</label>
                        <textarea name="keterangan" class="form-control" placeholder="Contoh: Harus rajin menabung biar cepat beli!"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah_target" class="btn btn-success w-100">Mulai Menabung!</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>