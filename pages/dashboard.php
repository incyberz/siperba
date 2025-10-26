<?php
// Pastikan variabel role sudah tersedia dari session
$role = $_SESSION['role'] ?? 'anggota';

// Tentukan file dashboard berdasarkan role
$file = "pages/dashboard-$role.php";

// Cek apakah file dashboard sesuai role ada
if (file_exists($file)) {
  include $file;
} else {
  echo "<div class='alert alert-warning mt-4'>Halaman dashboard untuk role <strong>$role</strong> belum tersedia.</div>";
}
