<?php
// pastikan hanya admin yang bisa akses
harusAdmin();

// validasi parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "<script>
    Swal.fire('Gagal', 'Parameter tidak valid.', 'error').then(() => window.location='?dashboard');
  </script>";
  exit;
}

$id = intval($_GET['id']);

// opsional: hapus data relasi lain (peserta & pengumpulan)
// mysqli_query($conn, "DELETE FROM tb_pengumpulan WHERE peserta_id IN (SELECT id FROM tb_peserta WHERE event_id=$id)");
// mysqli_query($conn, "DELETE FROM tb_peserta WHERE event_id=$id");

// hapus event
$hapus = mysqli_query($conn, "DELETE FROM tb_event WHERE id='$id'");

if ($hapus) {
  echo "<script>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Event berhasil dihapus.'
    }).then(() => window.location='?dashboard');
  </script>";
} else {
  $err = mysqli_error($conn);
  echo "<script>
    Swal.fire({
      icon: 'error',
      title: 'Gagal Menghapus!',
      text: 'Terjadi kesalahan: " . addslashes($err) . "'
    }).then(() => window.location='?dashboard');
  </script>";
}
