<?php include 'dokumen_hapus.php'; ?>

<h3>Event Aktif saat ini</h3>
<div class="row">
  <?php while ($active_event = mysqli_fetch_assoc($event_query)):
    $event_id = $active_event['id'];

    // Cek apakah user ini peserta event
    $peserta_res = mysqli_query($conn, "
        SELECT p.* 
        FROM tb_peserta p
        JOIN tb_user u ON u.id = p.user_id
        WHERE p.event_id = $event_id AND u.username = '$username'
    ");
    if (mysqli_num_rows($peserta_res) == 0) continue; // bukan peserta, skip

    $peserta = mysqli_fetch_assoc($peserta_res);

    // Cek apakah sudah upload berkas
    $pengumpulan_res = mysqli_query($conn, "SELECT * FROM tb_pengumpulan WHERE peserta_id = '$peserta[id]'");
    $jumlah_upload = mysqli_num_rows($pengumpulan_res);

  ?>
    <div class="col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <?= htmlspecialchars($active_event['nama_event']) ?>
        </div>
        <div class="card-body">
          <p>
            <strong>Batas Pengumpulan:</strong>
            <?php
            $batas = htmlspecialchars($active_event['batas_pengumpulan']);
            $eta = diffForHumans($batas);
            echo date('d M Y H:i', strtotime($batas)) . " ($eta)";

            ?>
          </p>
          <?php
          $Tambah = $jumlah_upload ? '➕ Tambah' : '';
          if ($jumlah_upload): ?>
            <div class="alert alert-success">
              Anda sudah upload <?= $jumlah_upload ?> dokumen.
            </div>

            <div class="mb-2">
              <strong>Berkas yang diupload:</strong>

            </div>
            <?php
            $no = 0;
            while ($pengumpulan = mysqli_fetch_assoc($pengumpulan_res)):
              $no++;
              $file_link = "<a target='_blank' href='berkas/" . htmlspecialchars($pengumpulan['nama_file']) . "'>$no.  " . htmlspecialchars($pengumpulan['nama_dokumen']) . " <i class='bi bi-file-earmark-text'></i></a>";
            ?>

              <div class=" d-flex justify-content-between border-top p-2">
                <div><?= $file_link ?></div>

                <form method="post" class="m-0 p-0">
                  <button name=btn_hapus_dokumen value="<?= $pengumpulan['id'] ?>" type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">Hapus</button>
                </form>
              </div>
            <?php endwhile; ?>

            <hr class="my-5">


          <?php else: ?>
            <div class="alert alert-danger"><b class="text-danger">Anda belum upload.</b></div>
          <?php endif; ?>
          <?php
          // ambil ekstensi yang diperbolehkan
          $ekstensi_files = $active_event['ekstensi_files']; // misal: "docx,xlsx,pdf,zip"
          $accept_attr = '';
          if (!empty($ekstensi_files)) {
            // ubah menjadi format .docx,.xlsx,.pdf,.zip
            $ext_array = explode(',', $ekstensi_files);
            $ext_array = array_map(fn($e) => '.' . trim($e), $ext_array);
            $accept_attr = implode(',', $ext_array);
          }
          ?>

          <form method="post" enctype="multipart/form-data" action="?dokumen_upload&event_id=<?= $event_id ?>">
            <h5 class="mb-3"><?= $Tambah ?> Upload Berkas</h5>
            <div class="mb-3">
              <label for="nama_dokumen">Nama Dokumen</label>
              <input type="text" id="nama_dokumen" name="nama_dokumen" class="form-control">
              <small class="text-muted">opsional, jika kosong otomatis disamakan dengan nama file Anda</small>
            </div>
            <div class="mb-3">
              <label for="berkas_<?= $event_id ?>" class="form-label">File Anda: <b class="text-danger">*</b></label>
              <input type="file" name="berkas" id="berkas_<?= $event_id ?>" class="form-control" required accept="<?= $accept_attr ?>">
              <?php if (!empty($accept_attr)): ?>
                <div class="form-text">Hanya diperbolehkan: <?= htmlspecialchars($ekstensi_files) ?></div>
              <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-success w-100">Upload</button>
          </form>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>