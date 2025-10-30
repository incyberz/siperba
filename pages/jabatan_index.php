<?php
harusAdmin(); // pastikan hanya admin yang boleh mengakses

// Hapus jabatan (jika ada parameter ?hapus=jabatan)
if (isset($_GET['hapus'])) {
  $hapus_jabatan = $_GET['hapus'];
  mysqli_query($conn, "DELETE FROM tb_jabatan WHERE jabatan='$hapus_jabatan'");
  echo "<script>
    Swal.fire({
      icon: 'success',
      title: 'Jabatan Dihapus',
      text: 'Jabatan $hapus_jabatan berhasil dihapus.'
    }).then(() => window.location='?jabatan_index');
  </script>";
  exit;
}

// Proses reset password (password = md5(jabatan))
if (isset($_GET['reset'])) {
  $jabatan = mysqli_real_escape_string($conn, $_GET['reset']);
  $password = md5($jabatan);

  $sql = "UPDATE tb_jabatan SET password='$password' WHERE jabatan='$jabatan'";
  if (mysqli_query($conn, $sql)) {
    echo "<script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Password berhasil direset menjadi jabatan.'
      }).then(() => window.location='?jabatan_index');
    </script>";
  } else {
    echo "<script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Password gagal direset.'
      });
    </script>";
  }
  exit;
}

include 'jabatan_tambah-process.php';

// ambil semua jabatan
$data = mysqli_query($conn, "SELECT * FROM tb_jabatan ORDER BY nama_jabatan");
?>

<div class="card shadow">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Manajemen Jabatan (Kategori Anggota)</h5>
    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#formTambahJabatan">
      <i class="bi bi-person-plus"></i> Tambah Jabatan
    </button>
  </div>

  <?php include 'jabatan_tambah.php'; ?>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Jabatan</th>
            <th>Nama Jabatan</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($data)):
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['jabatan']) ?></td>
              <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
              <td><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
              <td>
                <a href="?jabatan_index&hapus=<?= urlencode($row['jabatan']) ?>"
                  class="btn btn-sm btn-danger"
                  onclick="return confirm('Yakin ingin menghapus jabatan ini?')">
                  <i class="bi bi-trash"></i>
                </a>

              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>