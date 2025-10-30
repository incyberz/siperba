<?php
$select_jabatan = '';
include 'tb/tb_jabatan.php';

if ($tb_jabatan) {
  $opt = '';
  foreach ($tb_jabatan as $jabatan => $d) {
    $opt .= "<option value='$jabatan'>$d[nama_jabatan]</option>";
  }
  $select_jabatan = "
    <div class='col-md-3 mb-3'>
      <label class='form-label'>Jabatan</label>
      <select name='jabatan' class='form-select'>
        <option value=''>(tanpa jabatan)</option>
        $opt
      </select>
    </div>  
  ";
}



?>

<div class="collapse" id="formTambahUser">
  <div class="card-body border-bottom">
    <div class="text-muted mb-3" style="font-size: 10px;">User Tambah</div>
    <form method="POST">
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Username <b class="text-danger">*</b></label>
          <input type="text" name="username" id="username" class="input-username form-control" required>
          <script>
            $(document).ready(function() {
              $('#username').on('input', function() {
                let val = $(this).val().toLowerCase(); // ubah jadi huruf kecil
                val = val.replace(/[^a-z]/g, ''); // hapus karakter selain a-z
                $(this).val(val);
                $('#password').val(val);
              });
            });
          </script>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Password</label>
          <input type="text" id="password" class="form-control" disabled>
          <small class="text-muted">Secara default password sama dengan username.</small>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Nama <b class="text-danger">*</b></label>
          <input type="text" name="nama" class="form-control" required style="text-transform: capitalize;">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">No. WhatsApp <b class="text-danger">*</b></label>
          <input type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="62xxxx" required>
          <script src="assets/whatsapp.js"></script>
        </div>

      </div>
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Role <b class="text-danger">*</b></label>
          <select name="role" class="form-select">
            <option value="anggota">Anggota</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <?= $select_jabatan ?>
      </div>


      <button type="submit" name="btn_tambah_user" class="btn btn-success">
        <i class="bi bi-save"></i> Tambah User
      </button>

      <span class="btn btn-secondary" id="tambah_multi_user">
        <i class="bi bi-save"></i> Tambah Multi User
      </span>
      <script>
        $(function() {
          $('#tambah_multi_user').click(function() {
            let baris = prompt('Berapa jumlah user baru yang ingin Anda tambahkan?', 10);
            if (parseInt(baris) > 1) {
              location.replace(`?user_tambah_multiple&baris=${baris}`);
            }
          })
        })
      </script>

    </form>
  </div>
</div>