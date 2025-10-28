<?php
harusAdmin();

if (!isset($_GET['event_id'])) {
  echo "<script>
    Swal.fire('Oops!', 'Event belum dipilih.', 'warning')
      .then(() => window.location='?dashboard');
  </script>";
  exit;
}

$event_id = intval($_GET['event_id']);
if (!$event_id || $event_id < 1) {
  echo "<div class='alert alert-danger'>Invalid event_id.</div>";
  exit;
}

// Ambil data event
$event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_event WHERE id=$event_id"));
if (!$event) {
  echo "<script>
    Swal.fire('Error!', 'Event tidak ditemukan.', 'error')
      .then(() => window.location='?dashboard');
  </script>";
  exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // ambil semua peserta di event ini
  $q_peserta = mysqli_query($conn, "SELECT id FROM tb_peserta WHERE event_id=$event_id");
  while ($p = mysqli_fetch_assoc($q_peserta)) {
    $peserta_id = $p['id'];

    // cek apakah peserta ini sudah mengumpulkan berkas
    $cek = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_pengumpulan WHERE peserta_id=$peserta_id");
    $data = mysqli_fetch_assoc($cek);
    $sudah = intval($data['jml']);

    // hanya hapus jika belum ada pengumpulan
    if ($sudah == 0) {
      mysqli_query($conn, "DELETE FROM tb_peserta WHERE id=$peserta_id");
    }
  }

  if (!empty($_POST['anggota'])) {
    foreach ($_POST['anggota'] as $user_id) {
      $user_id = mysqli_real_escape_string($conn, $user_id);
      mysqli_query($conn, "INSERT INTO tb_peserta (event_id, user_id) VALUES ($event_id, '$user_id')");
    }
  }
  echo "<script>
    Swal.fire('Berhasil!', 'Data peserta berhasil diperbarui.', 'success')
      .then(() => window.location='?peserta_event&event_id=$event_id');
  </script>";
  exit;
}

// Ambil semua anggota
$anggota = mysqli_query($conn, "SELECT id, username, nama, whatsapp FROM tb_user WHERE role='anggota' ORDER BY nama");

// Ambil peserta event ini
$peserta_event = mysqli_query($conn, "SELECT 
b.username 
FROM tb_peserta a 
JOIN tb_user b ON a.user_id=b.id 
WHERE a.event_id=$event_id");
$peserta_list = [];
while ($p = mysqli_fetch_assoc($peserta_event)) {
  $peserta_list[] = $p['username'];
}
?>

<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">Kelola Peserta Event</h5>
  </div>
  <div class="card-body">
    <p><strong>Event:</strong> <?= htmlspecialchars($event['nama_event']) ?></p>
    <form method="post">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th width="50" class="text-center">
              <input type="checkbox" id="checkAll">
            </th>
            <th>Nama Anggota</th>
            <th>Username</th>
            <th>WhatsApp</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($anggota)):
            $checked = in_array($row['username'], $peserta_list) ? 'checked' : '';
          ?>
            <tr>
              <td class="text-center">
                <input class="checkItem" type="checkbox" name="anggota[]" value="<?= $row['id'] ?>" <?= $checked ?>>
              </td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td>
                <a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($row['whatsapp']) ?>" target="_blank">
                  <?= htmlspecialchars($row['whatsapp']) ?>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-save"></i> Simpan Perubahan
        </button>
        <a href="?dashboard" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  $(document).ready(function() {
    // toggle semua checkbox
    $('#checkAll').on('change', function() {
      $('.checkItem').prop('checked', $(this).is(':checked'));
    });

    // jika semua item dicentang manual, centang header juga
    $('.checkItem').on('change', function() {
      $('#checkAll').prop('checked', $('.checkItem:checked').length === $('.checkItem').length);
    });
  });
</script>