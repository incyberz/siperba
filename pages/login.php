<?php
if (isset($_POST['login'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = md5($_POST['password']);

  $q = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username' AND password='$password'");

  if (mysqli_num_rows($q) > 0) {
    $data = mysqli_fetch_assoc($q);
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['success'] = 'Selamat datang kembali, ' . $data['nama'];
    echo "<script>window.location='index.php?dashboard';</script>";
    exit;
  } else {
    echo "<script>
            $(function(){
              Swal.fire('Gagal','Username atau password salah!','error');
            });
          </script>";
  }
}
?>

<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
  <div class="card shadow-lg border-0" style="width: 360px;">
    <div class="card-body">
      <h4 class="text-center mb-3 fw-bold text-<?= $tema ?>">📁 <?= $nama_app ?></h4>
      <p class="text-center text-muted mb-4"><?= $nama_aplikasi ?></p>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control mb-1" required>
          <?php
          $text_wa = urlencode("*REQUEST RESET PASSWORD*\n\nYth. Admin $NAMA_APP\n\nSaya lupa password pada Aplikasi: $nama_app. Mohon bantuannya untuk reset password. Terimakasih.");
          $href_wa = "https://api.whatsapp.com/send?phone=$whatsapp_admin&text=$text_wa";
          ?>
          <small class="text-muted">jika lupa password, <a target="_blank" href="<?= $href_wa ?>">hubungi admin</a> untuk reset.</small>
        </div>

        <button type="submit" name="login" class="btn btn-<?= $tema ?> w-100">Login</button>
      </form>
    </div>
  </div>
</div>