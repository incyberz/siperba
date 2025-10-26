<?php
harusAdmin(); // pastikan hanya admin yang boleh mengakses

// Hapus user (jika ada parameter ?hapus=username)
if (isset($_GET['hapus'])) {
  $hapus_user = $_GET['hapus'];
  if ($hapus_user != $_SESSION['username']) { // admin tidak bisa hapus diri sendiri
    mysqli_query($conn, "DELETE FROM tb_user WHERE username='$hapus_user'");
    echo "<script>
      Swal.fire({
        icon: 'success',
        title: 'User Dihapus',
        text: 'User $hapus_user berhasil dihapus.'
      }).then(() => window.location='?user_index');
    </script>";
  } else {
    echo "<script>
      Swal.fire({
        icon: 'warning',
        title: 'Tidak Diizinkan!',
        text: 'Anda tidak bisa menghapus akun sendiri.'
      }).then(() => window.location='?user_index');
    </script>";
  }
  exit;
}

// Proses reset password (password = md5(username))
if (isset($_GET['reset'])) {
  $username = mysqli_real_escape_string($conn, $_GET['reset']);
  $password = md5($username);

  $sql = "UPDATE tb_user SET password='$password' WHERE username='$username'";
  if (mysqli_query($conn, $sql)) {
    echo "<script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Password berhasil direset menjadi username.'
      }).then(() => window.location='?user_index');
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

// Tambah user baru
if (isset($_POST['simpan'])) {
  $username = trim($_POST['username']);
  $password = md5($_POST['password']);
  $nama     = trim($_POST['nama']);
  $whatsapp = trim($_POST['whatsapp']);
  $role     = $_POST['role'] ?? 'anggota';

  // cek username duplikat
  $cek = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>
      Swal.fire('Gagal', 'Username sudah digunakan!', 'error');
    </script>";
  } else {
    $simpan = mysqli_query($conn, "INSERT INTO tb_user (username,password,nama,whatsapp,role,created_at,status)
              VALUES ('$username','$password','$nama','$whatsapp','$role',NOW(),1)");
    if ($simpan) {
      echo "<script>
        Swal.fire('Berhasil','User baru berhasil ditambahkan!','success')
          .then(()=>window.location='?user_index');
      </script>";
    } else {
      echo "<script>
        Swal.fire('Gagal','Terjadi kesalahan saat menyimpan.','error');
      </script>";
    }
  }
}

// ambil semua user
$data = mysqli_query($conn, "SELECT * FROM tb_user ORDER BY role DESC, nama ASC");
?>

<div class="card shadow">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Manajemen User</h5>
    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#formTambahUser">
      <i class="bi bi-person-plus"></i> Tambah User
    </button>
  </div>

  <div class="collapse" id="formTambahUser">
    <div class="card-body border-bottom">
      <form method="POST">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">No. WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control" placeholder="62xxxx" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="anggota">Anggota</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <button type="submit" name="simpan" class="btn btn-success">
          <i class="bi bi-save"></i> Simpan
        </button>
      </form>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Username</th>
            <th>Nama</th>
            <th>WhatsApp</th>
            <th>Role</th>
            <th>Status</th>
            <th>Terakhir Login</th>
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
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td>
                <a href="https://api.whatsapp.com/send?phone=<?= $row['whatsapp'] ?>" target="_blank" class="text-success">
                  <?= htmlspecialchars($row['whatsapp']) ?>
                </a>
              </td>
              <td>
                <span class="badge bg-<?= $row['role'] == 'admin' ? 'danger' : 'secondary' ?>">
                  <?= strtoupper($row['role']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td><?= htmlspecialchars($row['last_login'] ?? '-') ?></td>
              <td>
                <?php if ($row['username'] != $_SESSION['username']): ?>
                  <!-- Tombol Hapus -->
                  <a href="?user_index&hapus=<?= urlencode($row['username']) ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                    <i class="bi bi-trash"></i>
                  </a>








                  <?php
                  $isDefaultPass = ($row['password'] === md5($row['username']));
                  ?>

                  <?php if ($row['username'] != $_SESSION['username']): ?>
                    <?php if ($isDefaultPass): ?>
                      <!-- Password masih default, tampilkan ikon kunci abu -->
                      <span class="btn btn-sm btn-secondary" onclick="alert(`Password untuk user ini sudah sama dengan username.`)">
                        <i class="bi bi-lock"></i>
                      </span>
                    <?php else: ?>
                      <!-- Tombol Reset Password -->
                      <a href="?user_index&reset=<?= urlencode($row['username']) ?>"
                        class="btn btn-sm btn-warning"
                        onclick="return confirm('Yakin ingin mereset password user ini menjadi username?')">
                        <i class="bi bi-key"></i>
                      </a>
                    <?php endif; ?>
                  <?php else: ?>
                    <!-- Tidak bisa hapus atau reset diri sendiri -->
                    <button class="btn btn-sm btn-secondary" disabled>
                      <i class="bi bi-lock"></i>
                    </button>
                  <?php endif; ?>













                <?php else: ?>
                  <!-- Tidak bisa hapus atau reset diri sendiri -->
                  <button class="btn btn-sm btn-secondary" disabled>
                    <i class="bi bi-lock"></i>
                  </button>
                <?php endif; ?>

              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>