<?php
date_default_timezone_set("Asia/Jakarta");
$is_live = $_SERVER['SERVER_NAME'] == 'localhost' ? 0 : 1;

// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_siperba";

if ($is_live) {
  $user = 'febimasoem_admin_siperba';
  $pass = 'SistemPemantauanBerkas2025';
  $db = 'febimasoem_siperba';
}

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (mysqli_connect_errno()) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}
