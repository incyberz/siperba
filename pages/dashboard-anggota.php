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

// Ambil event aktif (batas pengumpulan belum lewat)
$today = date('Y-m-d H:i:s');
$sql_event = "SELECT * FROM tb_event WHERE batas_pengumpulan >= '$today' ORDER BY batas_pengumpulan ASC";
$event_query = mysqli_query($conn, $sql_event);

if (mysqli_num_rows($event_query) == 0): ?>
  <div class="alert alert-info">
    Saat ini tidak ada event aktif.
  </div>
<?php else: ?>

  <h3>Event Aktif saat ini</h3>
  <div class="row">
    <?php while ($event = mysqli_fetch_assoc($event_query)):
      $event_id = $event['id'];

      // Cek apakah user ini peserta event
      $peserta_res = mysqli_query($conn, "
        SELECT p.* 
        FROM tb_peserta p
        JOIN tb_user u ON u.id = p.user_id
        WHERE p.event_id = $event_id AND u.username = '$username'
    ");
      if (mysqli_num_rows($peserta_res) == 0) continue; // bukan peserta, skip

      $peserta = mysqli_fetch_assoc($peserta_res);

      // Cek apakah sudah upload berkas
      $pengumpulan_res = mysqli_query($conn, "
        SELECT * FROM tb_pengumpulan 
        WHERE peserta_id = " . $peserta['id'] . "
    ");
      $sudah_upload = mysqli_num_rows($pengumpulan_res) > 0;

    ?>
      <div class="col-md-6 mb-3">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <?= htmlspecialchars($event['nama_event']) ?>
          </div>
          <div class="card-body">
            <p>
              <strong>Batas Pengumpulan:</strong>
              <?php
              $batas = htmlspecialchars($event['batas_pengumpulan']);
              $eta = diffForHumans($batas);
              echo date('d M Y H:i', strtotime($batas)) . " ($eta)";

              ?>
            </p>
            <?php if ($sudah_upload): ?>
              <div class="alert alert-success">
                Anda sudah mengupload berkas.
              </div>
            <?php else: ?>
              <?php
              // ambil ekstensi yang diperbolehkan
              $ekstensi_files = $event['ekstensi_files']; // misal: "docx,xlsx,pdf,zip"
              $accept_attr = '';
              if (!empty($ekstensi_files)) {
                // ubah menjadi format .docx,.xlsx,.pdf,.zip
                $ext_array = explode(',', $ekstensi_files);
                $ext_array = array_map(fn($e) => '.' . trim($e), $ext_array);
                $accept_attr = implode(',', $ext_array);
              }
              ?>

              <form method="post" enctype="multipart/form-data" action="?upload_berkas&event_id=<?= $event_id ?>">
                <div class="mb-3">
                  <label for="berkas_<?= $event_id ?>" class="form-label">Upload Berkas</label>
                  <input type="file" name="berkas" id="berkas_<?= $event_id ?>" class="form-control" required accept="<?= $accept_attr ?>">
                  <?php if (!empty($accept_attr)): ?>
                    <div class="form-text">Hanya diperbolehkan: <?= htmlspecialchars($ekstensi_files) ?></div>
                  <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>

<hr class="my-5">

<?php include 'riwayat_pengumpulan.php';
