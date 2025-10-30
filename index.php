<?php
session_start();
include 'conn.php';
include 'config.php';


if (file_exists('config-febi.php')) include 'config-febi.php';
$NAMA_APP = ucwords($nama_app);
$isDefaultPass = '';

// Ambil nama page dari query string
$page = '';
foreach ($_GET as $key => $value) {
  $page = $key;
  break;
}

// Jika belum login, arahkan ke login
if (!isset($_SESSION['username'])) {
  $page = 'login';
}

// Ambil data user login jika sudah login
if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  include 'user.php';

  # ============================================================
  # INCLUDES
  # ============================================================
  include 'includes/diffForHumans.php';
  include 'includes/harusAdmin.php';
  include 'includes/jsurl.php';
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $nama_app ?> - <?= $nama_aplikasi ?></title>

  <link rel="stylesheet" href="assets/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/sweetalert2.css">

  <link rel="stylesheet" href="assets/main.css">

  <script src="assets/jquery-3.7.1.min.js"></script>
  <script src="assets/bootstrap.min.js"></script>
  <script src="assets/sweetalert2.all.min.js"></script>

</head>

<body class="bg-light" style="min-height: 100vh; background:linear-gradient(white,<?= $gradasi ?>)">
  <?php
  include 'header.php';
  $page = ($isDefaultPass and $page != 'logout') ? 'profil' : $page;
  ?>

  <div class="container mt-4">
    <?php
    $page = $page ? $page : "dashboard-$role";
    $file = "pages/$page.php";
    if (file_exists($file)) {
      include $file;
    } else {
      echo "<div class='alert alert-warning'>Halaman [$page] tidak ditemukan.</div>";
    }
    ?>
  </div>

  <script>
    $(function() {
      <?php if (isset($_SESSION['success'])): ?>
        Swal.fire('Sukses', '<?= $_SESSION['success'] ?>', 'success');
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>
    });
  </script>
  <footer>
    <div class="text-muted text-center py-5 small">Made with ♥️ Coding Albaiti DevTeam ©️2025</div>
  </footer>
</body>

</html>