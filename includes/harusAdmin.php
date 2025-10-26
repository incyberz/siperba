<?php
// pastikan session sudah dimulai di file pemanggil
if (!function_exists('harusAdmin')) {
  function harusAdmin()
  {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
      echo "<script>
        Swal.fire({
          icon: 'error',
          title: 'Akses Ditolak!',
          text: 'Halaman ini hanya untuk admin.'
        }).then(() => window.location='?dashboard');
      </script>";
      exit;
    }
  }
}
