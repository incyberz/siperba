<?php

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // ambil semua peserta di event ini
  $q_peserta = mysqli_query($conn, "SELECT id FROM tb_peserta WHERE event_id=$event_id");
  while ($p = mysqli_fetch_assoc($q_peserta)) {
    $peserta_id = $p['id'];

    // cek apakah peserta ini sudah mengumpulkan berkas
    $cek = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_pengumpulan WHERE peserta_id='$peserta_id'");
    $data = mysqli_fetch_assoc($cek);
    $sudah = intval($data['jml']);

    // hanya hapus jika belum ada pengumpulan
    if ($sudah == 0) {
      mysqli_query($conn, "DELETE FROM tb_peserta WHERE id='$peserta_id'");
    }
  }

  if (!empty($_POST['anggota'])) {
    foreach ($_POST['anggota'] as $user_id) {
      $user_id = mysqli_real_escape_string($conn, $user_id);
      mysqli_query($conn, "INSERT INTO tb_peserta (
        id, 
        event_id, 
        user_id
      ) VALUES (
        '$event_id-$user_id',
        $event_id, 
        '$user_id'
      ) ON DUPLICATE KEY UPDATE 
        assign_at = now()

      ");
    }
  }
  echo "<script>
    Swal.fire('Berhasil!', 'Data peserta berhasil diperbarui.', 'success')
      .then(() => window.location='?peserta_event&event_id=$event_id');
  </script>";
  exit;
}
