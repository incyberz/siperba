<?php
function diffForHumans($datetime)
{
  if (!$datetime) return '-';
  $now = new DateTime();
  $target = new DateTime($datetime);
  $diff = $now->diff($target);

  $isPast = $target < $now;

  // urutan dari terbesar ke terkecil
  if ($diff->y > 0) {
    $str = $diff->y . " tahun";
  } elseif ($diff->m > 0) {
    $str = $diff->m . " bulan";
  } elseif ($diff->d > 0) {
    $str = $diff->d . " hari";
  } elseif ($diff->h > 0) {
    $str = $diff->h . " jam";
  } elseif ($diff->i > 0) {
    $str = $diff->i . " menit";
  } else {
    $str = "beberapa detik";
  }

  return $isPast ? "$str yang lalu" : "$str lagi";
}
