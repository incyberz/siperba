<?php
// pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  echo "<script>
    Swal.fire({
      icon: 'error',
      title: 'Akses Ditolak!',
      text: 'Halaman ini hanya untuk admin.'
    }).then(() => window.location='?dashboard');
  </script>";
  exit;
}

if (isset($_POST['simpan'])) {

  // ambil dan amankan input
  $nama_event = mysqli_real_escape_string($conn, trim($_POST['nama_event']));
  $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));

  // input datetime dari <input type="datetime-local"> biasanya format "YYYY-MM-DDTHH:MM"
  // kita ubah menjadi "YYYY-MM-DD HH:MM:SS"
  function conv_dt($v)
  {
    $v = trim($v);
    if ($v === '') return '';
    // jika ada T, ganti dengan spasi
    $v = str_replace('T', ' ', $v);
    // jika hanya sampai menit, tambahkan :00 detik
    if (strlen($v) == 16) $v .= ':00';
    return $v;
  }

  $batas_pengumpulan_raw = $_POST['batas_pengumpulan'] ?? '';
  $tanggal_mulai_raw      = $_POST['tanggal_mulai'] ?? '';
  $tanggal_selesai_raw    = $_POST['tanggal_selesai'] ?? '';

  // validasi: batas_pengumpulan wajib
  if (trim($batas_pengumpulan_raw) == '') {
    echo "<script>
      Swal.fire('Gagal','Batas pengumpulan wajib diisi','error');
    </script>";
  } else {
    $batas_pengumpulan = conv_dt($batas_pengumpulan_raw);
    // tanggal_mulai: jika kosong -> sekarang (server)
    if (trim($tanggal_mulai_raw) == '') {
      $tanggal_mulai = '';
    } else {
      $tanggal_mulai = conv_dt($tanggal_mulai_raw);
    }
    // tanggal_selesai: jika kosong -> samakan dengan batas_pengumpulan
    if (trim($tanggal_selesai_raw) == '') {
      $tanggal_selesai = '';
    } else {
      $tanggal_selesai = conv_dt($tanggal_selesai_raw);
    }

    // insert ke tb_event
    $nama_event_q = mysqli_real_escape_string($conn, $nama_event);
    $deskripsi_q  = mysqli_real_escape_string($conn, $deskripsi);

    $deskripsi_q = $deskripsi_q ? "'$deskripsi_q'" : 'NULL';
    $tanggal_mulai = $tanggal_mulai ? "'$tanggal_mulai'" : 'NULL';
    $tanggal_selesai = $tanggal_selesai ? "'$tanggal_mulai'" : 'NULL';

    $sql = "INSERT INTO tb_event (
    nama_event, 
    deskripsi, 
    tanggal_mulai, 
    tanggal_selesai, 
    batas_pengumpulan, 
    created_at
    ) VALUES (
    '$nama_event_q', 
    $deskripsi_q, 
    $tanggal_mulai, 
    $tanggal_selesai, 
    '$batas_pengumpulan', 
    NOW()
    )";

    $simpan = mysqli_query($conn, $sql);

    if ($simpan) {
      // dapatkan id event baru
      $event_id = mysqli_insert_id($conn);

      // catatan: peserta dipilih pada halaman peserta (event_peserta.php)
      // jika ingin otomatis generate pengumpulan untuk semua peserta, lakukan di sana ketika peserta ditambahkan.

      echo "<script>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Event berhasil ditambahkan.'
        }).then(() => window.location='?dashboard');
      </script>";
    } else {
      $err = mysqli_error($conn);
      echo "<script>
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: 'Terjadi kesalahan saat menyimpan data: " . addslashes($err) . "'
        });
      </script>";
    }
  }
}
?>

<div class="card shadow">
  <div class="card-header bg-danger text-white">
    <h5 class="mb-0">Tambah Event Baru</h5>
  </div>
  <div class="card-body">
    <form method="POST" id="formEvent">
      <div class="mb-3">
        <label class="form-label">Nama Event <span class="text-danger">*</span></label>
        <input type="text" name="nama_event" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Mulai</label>
          <input type="datetime-local" name="tanggal_mulai" class="form-control" id="tanggal_mulai">
          <div class="form-text">Opsional — jika kosong akan otomatis terisi dengan waktu sekarang.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Selesai</label>
          <input type="datetime-local" name="tanggal_selesai" class="form-control" id="tanggal_selesai">
          <div class="form-text">Opsional — jika kosong akan disamakan dengan Batas Pengumpulan.</div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Batas Pengumpulan <span class="text-danger">*</span></label>
        <input type="datetime-local" name="batas_pengumpulan" class="form-control" id="batas_pengumpulan" required>
        <div class="form-text text-danger">Wajib diisi — peserta tidak bisa upload setelah melewati batas ini (nanti bisa dicek di logic upload).</div>
      </div>

      <button type="submit" name="simpan" class="btn btn-success">
        <i class="bi bi-save"></i> Simpan
      </button>
      <a href="?dashboard" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>

<script>
  $(function() {
    // Jika admin mengisi batas_pengumpulan, dan tanggal_selesai kosong, isi tanggal_selesai otomatis sama dengan batas_pengumpulan
    $('#batas_pengumpulan').on('change', function() {
      // const batas = $(this).val(); // format: YYYY-MM-DDTHH:MM
      // const tsel = $('#tanggal_selesai').val();
      // if (!tsel) {
      //   $('#tanggal_selesai').val(batas);
      // }
    });

    // Jika user mengosongkan tanggal_selesai setelah sebelumnya terisi, biarkan kosong — server akan set sama dengan batas saat submit
    // Form submission tetap mengandalkan validasi server untuk batas_pengumpulan
  });
</script>