<div class="collapse" id="formTambahUser">
  <div class="card-body border-bottom">
    <div class="text-muted mb-3" style="font-size: 10px;">User Tambah</div>
    <form method="POST">
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" required>
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
          <label class="form-label">Nama</label>
          <input type="text" name="nama" class="form-control" required style="text-transform: capitalize;">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">No. WhatsApp</label>
          <input type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="62xxxx" required>
          <script src="assets/whatsapp.js"></script>
        </div>

      </div>
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Role</label>
          <select name="role" class="form-select">
            <option value="anggota">Anggota</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <button type="submit" name="simpan" class="btn btn-success">
        <i class="bi bi-save"></i> Simpan
      </button>
    </form>
  </div>
</div>