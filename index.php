<?php
session_start();
include 'conn.php';

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
  $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username'"));

  # ============================================================
  # INCLUDES
  # ============================================================
  include 'includes/diffForHumans.php';
  include 'includes/harusAdmin.php';
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SiPerba</title>

  <link rel="stylesheet" href="assets/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/sweetalert2.css">

  <!-- Bootstrap Icons -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> -->


  <script src="assets/jquery-3.7.1.min.js"></script>
  <script src="assets/bootstrap.min.js"></script>
  <script src="assets/sweetalert2.all.min.js"></script>

</head>

<body class="bg-light">
  <?php if ($page != 'login') : ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?dashboard">📁 SiPerba</a>
        <div class="ms-auto">
          <span class="text-white me-3">
            Halo, <?= htmlspecialchars($user['nama']) ?> (<?= $user['role'] ?>)
          </span>
          <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
      </div>
    </nav>
  <?php endif; ?>

  <div class="container mt-4">
    <?php
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
</body>

</html>