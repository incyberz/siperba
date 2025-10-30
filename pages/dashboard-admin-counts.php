<?php
// Ambil statistik ringkas
$total_event        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_event"))['jml'];
$total_anggota      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_user WHERE role='anggota'"))['jml'];
$total_jabatan  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_jabatan"))['jml'];
?>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm text-center">
      <div class="card-body">
        <h1 class="fw-bold text-<?= $tema ?>"><?= $total_event ?></h1>
        <p class="text-muted mb-0">Total Event</p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm text-center position-relative">
      <a href="index.php?user_index" class="stretched-link"></a>
      <div class="card-body">
        <h1 class="fw-bold text-<?= $tema ?>"><?= $total_anggota ?></h1>
        <p class="text-muted mb-0">Total Anggota</p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm text-center position-relative">
      <a href="index.php?jabatan_index" class="stretched-link"></a>
      <div class="card-body">
        <h1 class="fw-bold text-<?= $tema ?>"><?= $total_jabatan ?></h1>
        <p class="text-muted mb-0">Jabatan Anggota</p>
      </div>
    </div>
  </div>


</div>