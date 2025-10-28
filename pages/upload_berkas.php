<?php

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'anggota') {
  echo "<script>
        Swal.fire('Akses Ditolak!', 'Hanya anggota yang bisa upload berkas.', 'error')
        .then(() => window.location='?login');
    </script>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['berkas'], $_GET['event_id'])) {
  $event_id = intval($_GET['event_id']);
  $username = $_SESSION['username'];

  // Ambil user id
  $user_res = mysqli_query($conn, "SELECT id FROM tb_user WHERE username='$username'");
  if (mysqli_num_rows($user_res) == 0) exit('User tidak ditemukan');
  $user = mysqli_fetch_assoc($user_res);
  $user_id = $user['id'];

  // Ambil peserta_id
  $peserta_res = mysqli_query($conn, "SELECT id FROM tb_peserta WHERE user_id=$user_id AND event_id=$event_id");
  if (mysqli_num_rows($peserta_res) == 0) exit('Anda bukan peserta event ini');
  $peserta = mysqli_fetch_assoc($peserta_res);
  $peserta_id = $peserta['id'];

  // Ambil ekstensi yang diperbolehkan
  $event_res = mysqli_query($conn, "SELECT ekstensi_files FROM tb_event WHERE id=$event_id");
  if (mysqli_num_rows($event_res) == 0) exit('Event tidak ditemukan');
  $event = mysqli_fetch_assoc($event_res);
  $allowed_ext = array_map('strtolower', array_map('trim', explode(',', $event['ekstensi_files'])));

  $file = $_FILES['berkas'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowed_ext)) {
    echo "<script>
            Swal.fire('Gagal!', 'Ekstensi file tidak diperbolehkan.', 'error')
            .then(() => window.history.back());
        </script>";
    exit;
  }

  // Buat nama file baru
  $date_str = date('YmdHis');
  $new_filename = $event_id . '-' . strtolower($username) . '-' . $date_str . '.' . $ext;
  $upload_dir = __DIR__ . '/../berkas/';
  if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

  $target_path = $upload_dir . $new_filename;

  if (move_uploaded_file($file['tmp_name'], $target_path)) {
    // simpan ke tb_pengumpulan
    $nama_dokumen = mysqli_real_escape_string($conn, $file['name']);
    $nama_file = mysqli_real_escape_string($conn, $new_filename);
    $sql = "INSERT INTO tb_pengumpulan (peserta_id, nama_dokumen, nama_file, created_at)
                VALUES ($peserta_id, '$nama_dokumen', '$nama_file', NOW())";
    mysqli_query($conn, $sql);

    echo "<script>
            Swal.fire('Berhasil!', 'File berhasil diupload.', 'success')
            .then(() => window.location='?dashboard');
        </script>";
    exit;
  } else {
    echo "<script>
            Swal.fire('Gagal!', 'File gagal diupload.', 'error')
            .then(() => window.history.back());
        </script>";
    exit;
  }
} else {
  echo "<script>
        Swal.fire('Gagal!', 'Tidak ada file yang diupload.', 'error')
        .then(() => window.history.back());
    </script>";
  exit;
}
