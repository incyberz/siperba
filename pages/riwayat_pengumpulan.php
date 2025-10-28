<?php
// Hanya anggota yang bisa akses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'anggota') {
  echo "<script>
        Swal.fire('Akses Ditolak!', 'Hanya anggota yang bisa mengakses halaman ini.', 'error')
        .then(() => window.location='?login');
    </script>";
  exit;
}

$username = $_SESSION['username'];

// Ambil semua peserta event untuk user ini
$sql_peserta = "
    SELECT p.id AS peserta_id, e.nama_event, e.batas_pengumpulan
    FROM tb_peserta p
    JOIN tb_user u ON u.id = p.user_id
    JOIN tb_event e ON e.id = p.event_id
    WHERE u.username = '$username'
    ORDER BY e.batas_pengumpulan DESC
";
$peserta_res = mysqli_query($conn, $sql_peserta);

?>

<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    Riwayat Pengumpulan Berkas
  </div>
  <div class="card-body">
    <?php if (mysqli_num_rows($peserta_res) == 0): ?>
      <div class="alert alert-info">Anda belum terdaftar di event manapun.</div>
    <?php else: ?>


      <div class="row row-cols-1 row-cols-md-2 g-3">
        <?php while ($row = mysqli_fetch_assoc($peserta_res)):
          $peserta_id = $row['peserta_id'];
          // Cek pengumpulan
          $pengumpulan_res = mysqli_query($conn, "SELECT * FROM tb_pengumpulan WHERE peserta_id = $peserta_id");
          if (mysqli_num_rows($pengumpulan_res) > 0) {
            $pengumpulan = mysqli_fetch_assoc($pengumpulan_res);
            $status_badge = "<span class='badge bg-success'>Sudah Upload</span>";
            $file_link = "<a target='_blank' href='berkas/" . htmlspecialchars($pengumpulan['nama_file']) . "'><i class='bi bi-file-earmark-text'></i> " . htmlspecialchars($pengumpulan['nama_dokumen']) . "</a>";
          } else {
            $status_badge = "<span class='badge bg-secondary'>Belum Upload</span>";
            $file_link = "-";
          }

          $batas_pengumpulan = diffForHumans($row['batas_pengumpulan']);
          $batas_info = "<p class='card-text'><strong>Batas Pengumpulan:</strong> $batas_pengumpulan</p>";
          $isClosed = strtotime($row['batas_pengumpulan']) - strtotime('now') < 0;
          if ($isClosed) $batas_info = '';
          $closedBadge = $isClosed ? "<span class='badge bg-danger'>Closed</span>" : '';
          $text_secondary = $isClosed ? 'text-secondary' : '';

        ?>
          <div class="col">
            <div class="card h-100">
              <div class="card-body <?= $text_secondary ?>">
                <h5 class="card-title"><?= htmlspecialchars($row['nama_event']) ?> <?= $closedBadge ?></h5>
                <?= $batas_info ?>
                <p class="card-text"><strong>Status:</strong> <?= $status_badge ?></p>
                <p class="card-text"><strong>Berkas:</strong> <?= $file_link ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

















    <?php endif; ?>
  </div>
</div>