<?php
include 'includes/ekstensi_show.php';

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
    $count_sudah = 0;
    $list_belum = '';
    $list_sudah = '';

    if ($total_peserta) {
      # ============================================================
      // select semua peserta untuk event ini
      # ============================================================
      $s = "SELECT
      a.id as id_peserta, 
      a.*, 
      b.*,
      (
        SELECT COUNT(1) FROM tb_pengumpulan 
        WHERE event_id=$event_id
        AND peserta_id=a.id) punya_berkas 
      FROM tb_peserta a 
      JOIN tb_user b ON a.user_id=b.id
      WHERE a.event_id=$event_id 
      ORDER BY b.nama 
      ";
      $q2 = mysqli_query($conn, $s) or die(mysqli_error($conn));
      $no_peserta_belum = 0;
      $no_peserta_sudah = 0;
      while ($peserta = mysqli_fetch_assoc($q2)) {
        $punyaBerkas = $peserta['punya_berkas'];

        $nama = htmlspecialchars($peserta['nama']);
        $whatsapp = htmlspecialchars($peserta['whatsapp']);

        $eta = diffForHumans($event['batas_pengumpulan']);
        $batas_pengumpulan = date('d M Y H:i', strtotime($event['batas_pengumpulan'])) . " ($eta)";
        $timestamp = date('M d, Y, H:i:s');
        $nama_peserta = ucwords(strtolower($nama));

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // ambil folder tanpa file

        $link_login = $protocol . $host . $path . '/';


        $list_dokumen = '';
        $tr_verifikasi = '';
        if ($punyaBerkas) {
          $count_sudah++;
          $no_peserta_sudah++;

          $text_wa = urlencode("============================\n*NOTIF $NAMA_APP*\n============================\nYth. $nama_peserta \n\nTerimakasih sudah mengumpulkan Berkas via $NAMA_APP pada event *$event[nama_event]*\n\nKami akan segera melakukan review berkas Anda. Terimakasih.\n\n[Admin $NAMA_APP, $timestamp]");

          // get list dokumen
          $s3 = "SELECT * FROM tb_pengumpulan 
          WHERE event_id=$event_id AND peserta_id='$peserta[id_peserta]'";
          $q3 = mysqli_query($conn, $s3) or die(mysqli_error($conn));
          $no_berkas = 0;
          while ($dokumen = mysqli_fetch_assoc($q3)) {
            $no_berkas++;
            $link = "<a class='d-block' target=_blank href='berkas/$dokumen[nama_file]'>$dokumen[nama_dokumen]</a>";
            $list_dokumen .= "<li>$link</li>";

            $tr_verifikasi .= "
              <tr>
                <td>
                  $no_berkas
                </td>
                <td>
                  $link
                </td>
                <td>
                  <button class='btn btn-sm btn-outline-primary' title='Accept Berkas'>
                    <i class='bi bi-check-circle'></i>
                  </button>

                  <button class='btn btn-sm btn-outline-danger' title='Tolak Berkas'>
                    <i class='bi bi-x-circle'></i>
                  </button>
                </td>
              </tr>
            ";
          }
        } else { // tidak punya berkas
          $no_peserta_belum++;
          $text_wa = urlencode("============================\n*NOTIF $NAMA_APP*\n============================\nYth. $nama_peserta \n\nMohon segera mengumpulkan Berkas via $NAMA_APP ($nama_aplikasi) untuk event:\n - *$event[nama_event]*\n - paling lambat *$batas_pengumpulan*\n\nLink login: $link_login\n_username_: $peserta[username]\n_password_: (password Anda) atau sama dengan username\n\nTerimakasih.\n\n[Admin $NAMA_APP, $timestamp]");
        } // endif tidak punya berkas

        $href_wa = "https://api.whatsapp.com/send?phone=$whatsapp&text=$text_wa";

        $link_wa_belum = $whatsapp ? "
          <a href='$href_wa' target='_blank' class='d-block btn btn-sm btn-danger' title='Kirim Notif'>
            <i class='bi bi-whatsapp'></i>
          </a>
        " : "
          <span class='d-block btn btn-sm btn-secondary update_whatsapp' id='update_whatsapp--$peserta[user_id]' title='Update Whatsapp'>
            <i class='bi bi-whatsapp'></i>
          </span>
        ";

        $no_peserta = $punyaBerkas ? $no_peserta_sudah : $no_peserta_belum;

        $debug = '';
        // $debug = " id. $peserta[id_peserta] ";

        $span_nama_peserta = "<span id=nama--$peserta[user_id]>$peserta[nama]</span>";

        if ($punyaBerkas) {
          $list_sudah .= "
          <div class='' style='border-top: solid 4px $border'>
              <div class='d-flex justify-content-between py-1'>
                <div>$no_peserta. $span_nama_peserta $debug</div>

                <div class='d-flex justify-content-center gap-1'>
                  <a href='$href_wa' target='_blank' class='btn btn-sm btn-outline-success' title='Hubungi via WhatsApp'>
                    <i class='bi bi-whatsapp'></i>
                  </a>

                  <button class='btn btn-sm btn-outline-primary' title='Setujui Pengumpulan' data-bs-toggle='collapse' data-bs-target='#verifikasi_berkas--$peserta[id_peserta]'>
                    <i class='bi bi-check-circle'></i>
                  </button>

                </div>
              </div>

              <ol class='border-top' style='font-size: 12px;'>
                $list_dokumen
              </ol>

              <div class='collapse' id='verifikasi_berkas--$peserta[id_peserta]'>
                <table class='table table-warning'>
                  $tr_verifikasi
                </table>
              </div>
          </div>
        ";
        } else {
          $list_belum .= "
          <div class='d-flex justify-content-between py-1' >
              <div>$no_peserta. $span_nama_peserta $debug </div>
              $link_wa_belum
          </div>
        ";
        }
      }
    }

    // Hitung yang belum mengumpulkan
    $count_belum = $total_peserta - $count_sudah;

    $batas = date('d-M-Y H:i', strtotime($event['batas_pengumpulan']));
    $eta = diffForHumans($event['batas_pengumpulan']);
    $batas = "$batas <div class='text-small text-muted'>($eta)</div>";
    $nama_event = htmlspecialchars($event['nama_event']);
    $ekstensi_files = htmlspecialchars($event['ekstensi_files']); // docx,xlsx,pdf,zip

    $ekstensi_show = ekstensi_show($ekstensi_files);


    $count_belum = $count_belum ? "$count_belum orang <i class='bi bi-info-circle'></i>" : '-';
    $count_sudah = $count_sudah ? "$count_sudah orang" : '-';

    $btn_hapus = "<a title='Hapus Event kosong.' href='?event_hapus&id=$event[id]' class='d-block mb-1 btn btn-sm btn-danger btn-hapus'><i class='bi bi-trash'></i></a>";
    $no_hapus = "<span class='d-block mb-1 btn btn-sm btn-secondary' onclick='alert(`Event sudah punya peserta.\n\nhapus dahulu jika ingin menghapus event.`)'><i class='bi bi-trash'></i></span>";

    $btn_hapus = $total_peserta ? $no_hapus : $btn_hapus;

    $selisih = strtotime($event['batas_pengumpulan']) - strtotime('now');
    $isClosed = $selisih < 0 ? 1 : 0;

    $closedBadge = $isClosed ? "<span class='badge bg-danger'>Closed</span>" : '';
    $table_danger = $isClosed ? 'table-danger' : '';
    $text_primary = $isClosed ? 'text-danger' : 'text-primary';
    $btn_edit = $isClosed ? '' : "<a title='Edit Event' href='?event_tambah&mode=ubah&event_id=$event[id]' class='d-block mb-1 btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>";
    $btn_peserta = $isClosed ? "<span class='d-block mb-1 btn btn-sm btn-secondary' onclick='alert(`Event closed.\n\nbatas pengumpulan berkas sudah terlampaui.`)'><i class='bi bi-people'></i></span>" : "<a title='Manage Peserta' href='?peserta_event&event_id=$event[id]' class='d-block mb-1 btn btn-sm btn-primary'><i class='bi bi-people'></i></a>";

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

<script src="assets/update_whatsapp.js"></script>