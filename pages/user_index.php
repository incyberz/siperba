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

include 'user_tambah-process.php';

// ambil semua user
$user_res = mysqli_query($conn, "SELECT a.id as user_id, a.* 
FROM tb_user a 
ORDER BY a.role DESC, a.jabatan, a.nama ASC");
?>

<div class="card shadow">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Manajemen User</h5>
    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#formTambahUser">
      <i class="bi bi-person-plus"></i> Tambah User
    </button>
  </div>

  <?php include 'user_tambah.php'; ?>

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
            <th>Jabatan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($user = mysqli_fetch_assoc($user_res)):
            $user_id = $user['user_id'];
            $whatsapp = $user['whatsapp'];
            $akhiran_whatsapp = substr($whatsapp, -4);

            $link_whatsapp = $whatsapp ? "
                <a href='https://api.whatsapp.com/send?phone=$user[whatsapp]'
                  target='_blank'
                  class='text-success'
                  data-bs-toggle='tooltip'
                  title='Chat via WhatsApp'>
                  <i class='bi bi-whatsapp fs-5'></i>
                </a>
              " : "
                <span onclick='alert(`Belum ada whatsapp untuk user ini.`)'
                  class='text-danger'
                  data-bs-toggle='tooltip'
                  title='WhatsApp belum ada'>
                  <i class='bi bi-slash-circle fs-5'></i>
                </span>
              ";



            $aksi_whatsapp = "
              $link_whatsapp

              <!-- Edit -->
              <span class='update_whatsapp text-warning' id='update_whatsapp--$user_id'
                role='button'
                data-bs-toggle='tooltip'
                title='Edit Nomor WhatsApp'>
                <i class='bi bi-pencil-square fs-5'></i>
              </span>

              <!-- Whatsapp User Hidden -->
              <span class='d-none' id='whatsapp--$user_id'>$user[whatsapp]</span>  
              
              <!-- Last 4 digit Whatsapp -->
              <div class=''> 
                <span class='text-muted' style='font-size:50%'>...$akhiran_whatsapp</span>  
              </div>
              
              
            ";
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><span id="nama--<?= $user_id ?>"><?= htmlspecialchars($user['nama']) ?></span></td>
              <td class="text-center align-middle">
                <div class="d-flex justify-content-center gap-2">
                  <?= $aksi_whatsapp ?>
                </div>
              </td>
              <td>
                <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'secondary' ?>">
                  <?= strtoupper($user['role']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($user['jabatan'] ?? '-') ?></td>
              <td><?= htmlspecialchars($user['status']) ?></td>
              <td>
                <?php if ($user['username'] != $_SESSION['username']): ?>
                  <!-- Tombol Hapus -->
                  <a href="?user_index&hapus=<?= urlencode($user['username']) ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                    <i class="bi bi-trash"></i>
                  </a>








                  <?php
                  $thisDefaultPass = ($user['password'] === md5($user['username']));
                  ?>

                  <?php if ($user['username'] != $_SESSION['username']): ?>
                    <?php if ($thisDefaultPass): ?>
                      <!-- Password masih default, tampilkan ikon kunci abu -->
                      <span class="btn btn-sm btn-secondary" onclick="alert(`Password untuk user ini sudah sama dengan username.`)">
                        <i class="bi bi-lock"></i>
                      </span>
                    <?php else: ?>
                      <!-- Tombol Reset Password -->
                      <a href="?user_index&reset=<?= urlencode($user['username']) ?>"
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

<script src="assets/update_whatsapp.js"></script>