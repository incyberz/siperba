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
$sql_event = "SELECT * FROM tb_event WHERE batas_pengumpulan >= '$now' ORDER BY batas_pengumpulan ASC";
$event_query = mysqli_query($conn, $sql_event);

if (mysqli_num_rows($event_query) == 0): ?>
  <div class="alert alert-info">
    Saat ini tidak ada event aktif.
  </div>
<?php
else:
  include 'event_active.php';
endif; ?>

<hr class="my-5">

<?php include 'riwayat_pengumpulan.php';
