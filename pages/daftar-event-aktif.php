<?php
if (isset($_POST['btn_hapus_dokumen'])) {
  $pengumpulan_id = intval($_POST['btn_hapus_dokumen']);

  // ambil data file dari database
  $q = mysqli_query($conn, "SELECT nama_file FROM tb_pengumpulan WHERE id=$pengumpulan_id");
  if ($row = mysqli_fetch_assoc($q)) {
    $nama_file = $row['nama_file'];
    $path = "berkas/$nama_file";

    // hapus file dari folder jika ada
    if (file_exists($path)) {
      if (!unlink($path)) die('Tidak bisa menghapus file lama.');
    }

    // hapus data di database
    mysqli_query($conn, "DELETE FROM tb_pengumpulan WHERE id=$pengumpulan_id");
  }

  jsurl(); // self redirect dg JS
}

?>

<div class="d-flex justify-content-between align-items-center mb-2">
  <h5 class="fw-semibold mb-0">📁 Daftar Event Aktif</h5>
  <div>
    <a href="index.php?event_tambah" class="btn btn-sm btn-<?= $tema ?>">+ Tambah Event</a>
  </div>
</div>

<?php
$no = 0;
$events = mysqli_query($conn, "SELECT a.*,
(
  SELECT COUNT(1) FROM tb_peserta 
  WHERE event_id=a.id) total_peserta,
(
  SELECT COUNT(1) FROM tb_pengumpulan 
  WHERE event_id=a.id) total_berkas
FROM tb_event a
ORDER BY a.batas_pengumpulan DESC");
$tr = "<tr><td colspan='100%' class='text-center text-muted'>Belum ada event.</td></tr>";

if (mysqli_num_rows($events) > 0) {
  $tr = '';

  while ($event = mysqli_fetch_assoc($events)) {
    $no++;

    // Hitung peserta total
    $event_id = $event['id'];
    $total_peserta = $event['total_peserta'];

    // Hitung peserta yang sudah mengumpulkan
    $query_sudah = mysqli_query($conn, "SELECT 
      a.id as pengumpulan_id,
      a.*,
      c.*  
      FROM tb_pengumpulan a
      JOIN tb_peserta b ON b.id = a.peserta_id 
      JOIN tb_user c ON b.user_id=c.id
      WHERE b.event_id=$event[id]
    ");

    $count_sudah = mysqli_num_rows($query_sudah);

    // Hitung yang belum mengumpulkan
    $count_belum = $total_peserta - $count_sudah;

    $batas = date('d-M-Y H:i', strtotime($event['batas_pengumpulan']));
    $eta = diffForHumans($event['batas_pengumpulan']);
    $batas = "$batas <div class='text-small text-muted'>($eta)</div>";
    $nama_event = htmlspecialchars($event['nama_event']);
    $ekstensi_files = htmlspecialchars($event['ekstensi_files']); // docx,xlsx,pdf,zip
    $ekstensi_show = '';

    $icon_map = [
      'doc' => 'bi-file-earmark-word',
      'docx' => 'bi-file-earmark-word',
      'xls' => 'bi-file-earmark-excel',
      'xlsx' => 'bi-file-earmark-excel',
      'pdf' => 'bi-file-earmark-pdf',
      'zip' => 'bi-file-earmark-zip',
      'rar' => 'bi-file-earmark-zip',
      'ppt' => 'bi-file-earmark-ppt',
      'pptx' => 'bi-file-earmark-ppt',
      'txt' => 'bi-file-earmark-text',
    ];

    $ekstensi_list = explode(',', $ekstensi_files);

    foreach ($ekstensi_list as $ext) {
      $ext = strtolower(trim($ext));
      $icon = $icon_map[$ext] ?? 'bi-file-earmark'; // default generic icon
      $ekstensi_show .= "<span class='me-1' title='$ext'><i class='bi $icon'></i></span>";
    }

    $list_belum = '';
    if ($count_belum) {
      $query_belum = mysqli_query($conn, "SELECT 
        b.username, b.nama, b.whatsapp
        FROM tb_peserta a
        JOIN tb_user b ON b.id = a.user_id
        LEFT JOIN tb_pengumpulan c ON c.peserta_id = a.id
        WHERE a.event_id = $event_id AND c.id IS NULL
        ORDER BY b.nama
      ");


      $s = "SELECT 
      a.id as id_peserta,
      a.*, 
      b.*,
      (SELECT 1 FROM tb_pengumpulan WHERE event_id=$event_id AND peserta_id=a.id) punya_berkas
      FROM tb_peserta a 
      JOIN tb_user b ON a.user_id=b.id 
      WHERE a.event_id = $event_id
      ";
      $q = mysqli_query($conn, $s) or die(mysqli_error($conn));

      $j = 0;
      while ($d = mysqli_fetch_assoc($q)) {

        $j++;
        $nama = htmlspecialchars($d['nama']);
        $whatsapp = htmlspecialchars($d['whatsapp']);

        $eta = diffForHumans($event['batas_pengumpulan']);
        $batas_pengumpulan = date('d M Y H:i', strtotime($event['batas_pengumpulan'])) . " ($eta)";
        $timestamp = date('M d, Y, H:i:s');
        $nama_peserta = ucwords(strtolower($nama));

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // ambil folder tanpa file

        $link_login = $protocol . $host . $path . '/';

        $text_wa = urlencode("============================\n*NOTIF $NAMA_APP*\n============================\nYth. $nama_peserta \n\nMohon segera mengumpulkan Berkas via $NAMA_APP ($nama_aplikasi) untuk event:\n - *$event[nama_event]*\n - paling lambat *$batas_pengumpulan*\n\nLink login: $link_login\n_username_: $d[username]\n_password_: (password Anda) atau sama dengan username\n\nTerimakasih.\n\n[Admin $NAMA_APP, $timestamp]");
        $wa_link = "https://api.whatsapp.com/send?phone=$whatsapp&text=$text_wa";


        if (!$d['punya_berkas']) {
          $list_belum .= "
            <div class='d-flex justify-content-between align-items-center py-1 border-bottom'>
                <div>$j. $d[nama]</div>
                <div>
                    <a href='$wa_link' target='_blank' class='btn btn-sm btn-danger'>
                        <i class='bi bi-whatsapp'></i> Z $d[punya_berkas]
                    </a>
                </div>
            </div>
          ";
        }
      }



      $j = 0;
      while ($row = mysqli_fetch_assoc($query_belum)) {
        $j++;
        $nama = htmlspecialchars($row['nama']);
        $whatsapp = htmlspecialchars($row['whatsapp']);

        $eta = diffForHumans($event['batas_pengumpulan']);
        $batas_pengumpulan = date('d M Y H:i', strtotime($event['batas_pengumpulan'])) . " ($eta)";
        $timestamp = date('M d, Y, H:i:s');
        $nama_peserta = ucwords(strtolower($nama));

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // ambil folder tanpa file

        $link_login = $protocol . $host . $path . '/';

        $text_wa = urlencode("============================\n*NOTIF $NAMA_APP*\n============================\nYth. $nama_peserta \n\nMohon segera mengumpulkan Berkas via $NAMA_APP ($nama_aplikasi) untuk event:\n - *$event[nama_event]*\n - paling lambat *$batas_pengumpulan*\n\nLink login: $link_login\n_username_: $row[username]\n_password_: (password Anda) atau sama dengan username\n\nTerimakasih.\n\n[Admin $NAMA_APP, $timestamp]");
        $wa_link = "https://api.whatsapp.com/send?phone=$whatsapp&text=$text_wa";

        $list_belum .= "
          <div class='d-flex justify-content-between align-items-center py-1 border-bottom'>
              <div>$j. $nama</div>
              <div>
                  <a href='$wa_link' target='_blank' class='btn btn-sm btn-danger'>
                      <i class='bi bi-whatsapp'></i>
                  </a>
              </div>
          </div>
        ";
      }
    }

    $list_sudah = '';
    if ($count_sudah) {
      $j = 0;
      while ($row = mysqli_fetch_assoc($query_sudah)) {
        $j++;
        $nama = htmlspecialchars($row['nama']);
        $whatsapp = htmlspecialchars($row['whatsapp']);

        $timestamp = date('M d, Y, H:i:s');
        $nama_peserta = ucwords(strtolower($nama));

        $text_wa = urlencode("============================\n*NOTIF $NAMA_APP*\n============================\nYth. $nama_peserta \n\nTerimakasih sudah mengumpulkan Berkas via $NAMA_APP pada event *$event[nama_event]*\n\nKami akan segera melakukan review berkas Anda. Terimakasih.\n\n[Admin $NAMA_APP, $timestamp]");

        $wa_link = "https://api.whatsapp.com/send?phone=$whatsapp&text=$text_wa";

        $nama_dokumen = htmlspecialchars($row['nama_dokumen']);
        $nama_file = htmlspecialchars($row['nama_file']);
        $path_file = "berkas/$nama_file";

        if (!empty($nama_file) && file_exists($path_file)) {
          $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

          // mapping ekstensi → icon Bootstrap
          $icon_map = [
            'doc' => 'bi-file-earmark-word',
            'docx' => 'bi-file-earmark-word',
            'xls' => 'bi-file-earmark-excel',
            'xlsx' => 'bi-file-earmark-excel',
            'pdf' => 'bi-file-earmark-pdf',
            'zip' => 'bi-file-earmark-zip',
            'rar' => 'bi-file-earmark-zip',
            'ppt' => 'bi-file-earmark-ppt',
            'pptx' => 'bi-file-earmark-ppt',
            'txt' => 'bi-file-earmark-text',
          ];

          // pilih ikon sesuai ekstensi, default generic
          $icon_class = $icon_map[$ext] ?? 'bi-file-earmark';

          // buat link dokumen dengan ikon
          $link_dokumen = "
            <a class='btn btn-sm btn-outline-success' target='_blank' href='$path_file'><i class='bi $icon_class'></i></a>
            <form method=post class='d-inline'>
              <button class='btn btn-sm btn-danger' onclick='return confirm(`Yakin hapus dokumen ini?`)' name=btn_hapus_dokumen value='$row[pengumpulan_id]'><i class='bi bi-trash'></i></button>
            </form>
          ";
        } else {
          $link_dokumen = "<span class='text-danger'><i class='bi bi-exclamation-triangle-fill'></i> Berkas Hilang</span>";
        }


        $list_sudah .= "
          <div class='py-1 border-bottom'>
            <div class='d-flex justify-content-between'>
              <div>$j. $nama</div>
              <div class='d-flex gap-1'>
                <div>
                  $link_dokumen
                </div>
                <div>
                  <a href='$wa_link' target='_blank' class='btn btn-sm btn-success'>
                      <i class='bi bi-whatsapp'></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        ";
      }
    }

    $count_belum = $count_belum ? "$count_belum orang <i class='bi bi-info-circle'></i>" : '-';
    $count_sudah = $count_sudah ? "$count_sudah orang" : '-';

    $btn_hapus = "<a href='?event_hapus&id=$event[id]' class='d-block mb-1 btn btn-sm btn-danger btn-hapus'><i class='bi bi-trash'></i></a>";
    $no_hapus = "<span class='d-block mb-1 btn btn-sm btn-secondary' onclick='alert(`Event sudah punya peserta.\n\nhapus dahulu jika ingin menghapus event.`)'><i class='bi bi-trash'></i></span>";

    $btn_hapus = $total_peserta ? $no_hapus : $btn_hapus;

    $selisih = strtotime($event['batas_pengumpulan']) - strtotime('now');
    $isClosed = $selisih < 0 ? 1 : 0;

    $closedBadge = $isClosed ? "<span class='badge bg-danger'>Closed</span>" : '';
    $table_danger = $isClosed ? 'table-danger' : '';
    $text_primary = $isClosed ? 'text-danger' : 'text-primary';
    $btn_edit = $isClosed ? '' : "<a href='?event_tambah&mode=ubah&event_id=$event[id]' class='d-block mb-1 btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>";
    $btn_peserta = $isClosed ? "<span class='d-block mb-1 btn btn-sm btn-secondary' onclick='alert(`Event closed.\n\nbatas pengumpulan berkas sudah terlampaui.`)'><i class='bi bi-people'></i></span>" : "<a href='?peserta_event&event_id=$event[id]' class='d-block mb-1 btn btn-sm btn-primary'><i class='bi bi-people'></i></a>";

    $separator = '';

    $tr .= "
      <tr class='$table_danger' style='border-top: solid 5px $border'>
        <td>$no</td>
        <td>
          <b class='$text_primary'>$nama_event $closedBadge</b>

          <div class=my-2>
            <b>deadline:</b> $batas
          </div>
          <div class=my-2>
            $ekstensi_show
          </div>

          <div class='d-flex gap-1'>
            <div class=flex-1>
              $btn_peserta
            </div>
            <div class=flex-1>
              $btn_edit
            </div>
            <div class=flex-1>
              $btn_hapus
            </div>
          </div>

        </td>
        <td class='table-success text-success'>
          <span class='badge bg-success mb-2'>$count_sudah</span>
          <div>
            $list_sudah
          </div>
        </td>
        <td  class='table-danger text-danger'>
          <span class='badge bg-danger mb-2'>$count_belum</span>
          <div class='list-group list-group-flush'>
            $list_belum
          </div>
        </td>
      </tr>
    ";
  }
}
?>

<div class="card border-0 shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-hover align-top mb-0">
      <thead class="table-info">
        <tr>
          <th width="5%">#</th>
          <th>Nama Event</th>
          <th class="text-success table-success">Sudah Upload Berkas</th>
          <th class="text-danger table-danger">Belum Upload</th>
        </tr>
      </thead>
      <tbody>
        <?= $tr ?>
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