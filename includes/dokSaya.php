<?php
if (!function_exists('dokSaya')) {
  function dokSaya($pengumpulan, $peserta)
  {
    if (!$pengumpulan) return false;
    if (!$peserta) return false;
    return $pengumpulan['peserta_id'] === $peserta['id'];
  }
}
if (!function_exists('dokSaya2')) {
  function dokSaya2($pengumpulan_id, $conn, $username)
  {
    $pengumpulan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_pengumpulan WHERE id=$pengumpulan_id"));
    $peserta = mysqli_fetch_assoc(mysqli_query($conn, "SELECT a.* FROM tb_peserta a 
    JOIN tb_user b ON a.user_id=b.id 
    JOIN tb_event c ON a.event_id=c.id
    WHERE b.username='$username' 
    AND c.id=$pengumpulan[event_id]"));

    if (!$pengumpulan) return false;
    if (!$peserta) return false;
    return $pengumpulan['peserta_id'] === $peserta['id'];
  }
}
