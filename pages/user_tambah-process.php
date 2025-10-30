<?php

// Tambah user baru
if (isset($_POST['btn_tambah_user'])) {
  $username = trim($_POST['username']);
  $password = md5($_POST['username']);
  $nama     = ucwords(trim($_POST['nama']));
  $whatsapp = trim($_POST['whatsapp']);
  $role     = $_POST['role'] ?? 'anggota';
  $jabatan = $_POST['jabatan'];
  $jabatan_or_null = $jabatan ? "'$jabatan'" : 'NULL';

  // cek username duplikat
  $cek = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>
      Swal.fire('Gagal', 'Username sudah digunakan!', 'error');
    </script>";
  } else {
    $simpan = mysqli_query($conn, "INSERT INTO tb_user (
      username,
      password,
      nama,
      whatsapp,
      role,
      created_at,
      status,
      jabatan
    ) VALUES (
      '$username',
      '$password',
      '$nama',
      '$whatsapp',
      '$role',
      NOW(),
      1,
      $jabatan_or_null
    )");

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
