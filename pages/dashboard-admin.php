<?php
// Ambil statistik ringkas
$total_event        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_event"))['jml'];
$total_peserta      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_peserta"))['jml'];
$total_pengumpulan  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_pengumpulan"))['jml'];
?>

<h4 class="fw-semibold mb-4">📊 Dashboard Admin</h4>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm text-center">
      <div class="card-body">
        <h1 class="fw-bold text-danger"><?= $total_event ?></h1>
        <p class="text-muted mb-0">Total Event</p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm text-center">
      <div class="card-body">
        <h1 class="fw-bold text-danger"><?= $total_peserta ?></h1>
        <p class="text-muted mb-0">Total Peserta</p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm text-center">
      <div class="card-body">
        <h1 class="fw-bold text-danger"><?= $total_pengumpulan ?></h1>
        <p class="text-muted mb-0">Total Pengumpulan</p>
      </div>
    </div>
  </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-2">
  <h5 class="fw-semibold mb-0">📁 Daftar Event Aktif</h5>
  <div>

    <a href="index.php?user_index" class="btn btn-sm btn-danger">Kelola Anggota</a>
    <a href="index.php?event_tambah" class="btn btn-sm btn-danger">+ Tambah Event</a>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0">
      <thead class="table-danger">
        <tr>
          <th width="5%">#</th>
          <th>Nama Event</th>
          <th>Batas Pengumpulan</th>
          <th>Keterangan</th>
          <th width="22%">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $result = mysqli_query($conn, "SELECT * FROM tb_event ORDER BY id DESC");
        if (mysqli_num_rows($result) > 0) :
          while ($row = mysqli_fetch_assoc($result)) :
        ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama_event']) ?></td>
              <td>
                <?php
                $batas = date('d-M-Y H:i', strtotime($row['batas_pengumpulan']));
                $eta = diffForHumans($row['batas_pengumpulan']);
                echo "$batas <div class='text-small text-muted'>($eta)</div>";
                ?>
              </td>
              <td><?= $row['deskripsi'] ?? '-' ?></td>
              <td>
                <a href="?peserta_event&event_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">👥 Peserta</a>
                <a href="?event_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                <a href="?event_hapus&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger btn-hapus">🗑️ Hapus</a>
              </td>
            </tr>
        <?php
          endwhile;
        else :
          echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada event.</td></tr>";
        endif;
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  $(document).on('click', '.btn-hapus', function(e) {
    e.preventDefault();
    let href = $(this).attr('href');
    Swal.fire({
      title: 'Yakin hapus event ini?',
      text: 'Data peserta & pengumpulan terkait juga akan terhapus!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = href;
      }
    });
  });
</script>