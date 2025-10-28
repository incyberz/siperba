<?php
if (!$user) die("User tidak ditemukan.");

// proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $msg = '';
  $success = 'success';
  $id = intval($user['id']);

  // ubah nama
  if (isset($_POST['btn_ubah_nama'])) {
    $nama = ucwords(mysqli_real_escape_string($conn, $_POST['nama']));
    mysqli_query($conn, "UPDATE tb_user SET nama='$nama' WHERE id=$id");
    $msg = "Nama berhasil diperbarui.";
  }

  // ubah username
  if (isset($_POST['btn_ubah_username'])) {
    $new_username = preg_replace('/[^a-z]/', '', strtolower($_POST['username']));
    $cek = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$new_username' AND id!=$id");
    if (strlen($new_username) < $minlength_username) {
      $msg = "Username terlalu pendek.";
      $success = 'danger';
    } elseif (mysqli_num_rows($cek) > 0) {
      $msg = "Username sudah digunakan.";
      $success = 'danger';
    } else {
      mysqli_query($conn, "UPDATE tb_user SET username='$new_username' WHERE id=$id");
      $_SESSION['username'] = $new_username;
      $msg = "Username berhasil diubah.";
    }
  }

  // ubah whatsapp
  if (isset($_POST['btn_ubah_whatsapp'])) {
    $wa = preg_replace('/[^0-9]/', '', $_POST['whatsapp']);
    if (strpos($wa, '08') === 0) $wa = '628' . substr($wa, 2);
    if (strpos($wa, '628') !== 0) $wa = '';
    if ($wa != '') {
      mysqli_query($conn, "UPDATE tb_user SET whatsapp='$wa' WHERE id=$id");
      $msg = "Nomor WhatsApp diperbarui.";
    } else {
      $success = 'danger';
      $msg = "Nomor WhatsApp tidak valid.";
    }
  }

  // ubah password
  if (isset($_POST['btn_ubah_password'])) {
    $password_lama = $_POST['password_lama'];
    // cek dengan konsisi password lama

    // ambil password lama dari database
    $sql_pass = mysqli_query($conn, "SELECT password FROM tb_user WHERE username='$username' LIMIT 1");
    $row_pass = mysqli_fetch_assoc($sql_pass);
    $password_db = $row_pass['password'];

    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];
    if ($password_baru == $username) {
      $msg = "Password baru tidak boleh sama dengan username.";
      $success = 'danger';
    } elseif (md5($password_lama) != $password_db) {
      $msg = "Password lama tidak sesuai.";
      $success = 'danger';
    } elseif ($password_baru === $password_konfirmasi && strlen($password_baru) >= $minlength_password) {
      mysqli_query($conn, "UPDATE tb_user SET password=md5('$password_baru') WHERE id=$id");
      $msg = "Password berhasil diubah.";
    } else {
      $success = 'danger';
      $msg = "Password tidak cocok atau terlalu pendek.";
    }
  }

  echo "<div class='alert alert-$success mt-3 text-center'>$msg</div>";

  // refresh data user
  jsurl('', 2000);
}
?>

<div class="col-lg-6 mx-auto">
  <h4 class="mb-3">Profil Saya</h4>

  <div class="card shadow-sm">
    <div class="card-body">

      <?php if ($isDefaultPass): ?>
        <div class="alert alert-warning">
          ⚠️ <b>Password Anda masih default</b> (sama dengan username). Untuk keamanan Anda wajib mengubahnya!
        </div>
      <?php else : ?>

        <form method="post">
          <label class="form-label">Nama Lengkap: <b id=span--nama><?= htmlspecialchars($user['nama']) ?></b></label>
          <input type="text" name="nama" class="form-control" required minlength="3" maxlength="30">
          <button type="submit" name="btn_ubah_nama" class="btn btn-primary mt-2 w-100">Ubah Nama</button>
        </form>

        <hr class="my-5">

        <form method="post">

          <label class="form-label">Username: <b id=span--username><?= htmlspecialchars($user['username']) ?></b></label>
          <input minlength="<?= $minlength_username ?? 3 ?>" type="text" name="username" id="username" class="form-control" required>
          <small class="d-block text-muted">minimal <?= $minlength_username ?? 3 ?> huruf</small>
          <button type="submit" name="btn_ubah_username" class="btn btn-warning mt-2 w-100">Ubah Username</button>
        </form>
        <hr class="my-5">

        <form method="post">

          <label class="form-label">Nomor WhatsApp: <b id=span--whatsapp><?= htmlspecialchars($user['whatsapp']) ?></b></label>
          <input type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="62xxxx" required minlength="11" maxlength="14">
          <button type="submit" name="btn_ubah_whatsapp" class="btn btn-success mt-2 w-100">Ubah WhatsApp</button>
        </form>
        <hr class="my-5">

      <?php endif; ?>


      <form method="post">
        <label class="form-label">Password Anda saat ini</label>
        <?php
        if ($isDefaultPass) {
          echo "
              <input type='text' class='form-control' value='$username' disabled>
              <input type='hidden' name=password_lama class='form-control' value='$username'>
            ";
        } else {
          echo "<input required type='password' name='password_lama' class='form-control' minlength='$minlength_password' placeholder='password lama...'>";
        }
        ?>
        <label class="form-label mt-2">Password Baru</label>
        <input required type="password" name="password_baru" class="form-control" minlength="<?= $minlength_password ?? 3 ?>" placeholder="password baru...">
        <label class="form-label mt-2">Konfirmasi Password</label>
        <input required type="password" name="password_konfirmasi" class="form-control" minlength="<?= $minlength_password ?? 3 ?>" placeholder="confirm password...">
        <small class="d-block text-muted">minimal <?= $minlength_password ?? 3 ?> karakter</small>
        <button type="submit" name="btn_ubah_password" class="btn btn-danger mt-2 w-100">Ubah Password</button>
      </form>

    </div>
  </div>
</div>

<script src="assets/whatsapp.js"></script>
<script src="assets/username.js"></script>