<?php
$tb_jabatan = [];
if (isset($conn)) {
  $s = "SELECT * FROM tb_jabatan ORDER BY nama_jabatan";
  $q = mysqli_query($conn, $s) or die(mysqli_error($conn));
  while ($d = mysqli_fetch_assoc($q)) {
    $tb_jabatan[$d['jabatan']] = $d;
  }
}
