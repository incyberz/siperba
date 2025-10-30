<?php
include 'includes/dokSaya.php';

if (isset($_POST['btn_hapus_dokumen'])) {
  $pengumpulan_id = intval($_POST['btn_hapus_dokumen']);
  if (!$pengumpulan_id) die('invalid pengumpulan_id');

  if (!dokSaya2($pengumpulan_id, $conn, $username)) {
    die('Anda tidak berhak akses dokumen ini.');
  };

  $pengumpulan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_pengumpulan WHERE id=$pengumpulan_id"));
  if (!unlink("berkas/$pengumpulan[nama_file]")) die('Gagal menghapus file ini.');

  $s = "DELETE FROM tb_pengumpulan WHERE id = $pengumpulan_id";
  $q = mysqli_query($conn, $s) or die(mysqli_error($conn));
  jsurl();



  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  exit;
}
