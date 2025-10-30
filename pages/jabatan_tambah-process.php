<?php
// Tambah jabatan baru
if (isset($_POST['btn_tambah_jabatan'])) {
  $jabatan = trim($_POST['jabatan']);
  $nama_jabatan     = ucwords(trim($_POST['nama_jabatan']));
  $deskripsi = trim($_POST['deskripsi']);

  $deskripsi_or_null = $deskripsi ? "'$deskripsi'" : 'NULL';

  $s = "INSERT INTO tb_jabatan (
    jabatan,
    nama_jabatan,
    deskripsi
  ) VALUES (
    '$jabatan',
    '$nama_jabatan',
    $deskripsi_or_null
  )
  ";

  $simpan = mysqli_query($conn, $s);

  if ($simpan) {
    echo "<script>
      Swal.fire('Berhasil','Jabatan baru berhasil ditambahkan!','success')
        .then(()=>window.location='?jabatan_index');
    </script>";
  } else {
    echo "<script>
      Swal.fire('Gagal','Terjadi kesalahan saat menyimpan.','error');
    </script>";
  }
}
