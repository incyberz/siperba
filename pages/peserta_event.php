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

# ============================================================
# PROCESS PESERTA EVENT
# ============================================================
include 'peserta_event-process.php';

# ============================================================
# MANAGE PESERTA EVENT
# ============================================================
// Ambil semua anggota
$anggota_res = mysqli_query($conn, "SELECT * FROM tb_user WHERE role='anggota' ORDER BY jabatan, nama");
if (!mysqli_num_rows($anggota_res)) {
  echo "<script>
    Swal.fire('Error!', 'Belum ada anggota satupun.', 'error')
      .then(() => window.location='?dashboard');
  </script>";
  exit;
}


$cb_jabatan = '';
include 'tb/tb_jabatan.php';

if ($tb_jabatan) {
  $opt = '';
  foreach ($tb_jabatan as $jabatan => $d) {
    $opt .= "<label class='d-block'><input class='cb_jabatan' id='cd_jabatan--$jabatan' type=checkbox value='$jabatan'> $d[nama_jabatan]</label>";
  }
  $cb_jabatan = "
    <div class='d-flex gap-3 mb-3'>
      <div><i class='small text-muted'>Check by jabatan : </i></div>
      $opt
    </div>
  ";

?>
  <script>
    $(function() {
      $('.cb_jabatan').click(function() {
        // let tid = $(this).prop('id');
        // let rid = tid.split('--');
        // let aksi = rid[0];
        // let id = rid[1];
        let val = $(this).val();
        let checked = $(this).prop('checked');
        console.log(val, checked);
        $('.cb_jabatan--' + val).prop('checked', checked);
      })

    })
  </script>


<?php
}



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

    <?= $cb_jabatan ?>

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
            <th>Jabatan</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($anggota = mysqli_fetch_assoc($anggota_res)):
            $checked = in_array($anggota['username'], $peserta_list) ? 'checked' : '';
            $jabatan = $anggota['jabatan'] ?? '<i class="small text-muted">(tanpa jabatan)</i>';
          ?>
            <tr>
              <td class="text-center">
                <input class="checkItem cb_jabatan--<?= $anggota['jabatan'] ?>" type="checkbox" name="anggota[]" value="<?= $anggota['id'] ?>" <?= $checked ?>>
              </td>
              <td><?= htmlspecialchars($anggota['nama']) ?></td>
              <td><?= htmlspecialchars($anggota['username']) ?></td>
              <td>
                <a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($anggota['whatsapp']) ?>" target="_blank">
                  <?= htmlspecialchars($anggota['whatsapp']) ?>
                </a>
              </td>
              <td>
                <?= $jabatan ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-save"></i> Simpan Peserta Event
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