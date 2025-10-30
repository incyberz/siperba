<div class="collapse" id="formTambahJabatan">
  <div class="card-body border-bottom">
    <div class="text-muted mb-3" style="font-size: 10px;">Jabatan Tambah</div>
    <form method="POST">
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Key Jabatan</label>
          <input type="text" name="jabatan" id="jabatan" class="form-control" required placeholder="contoh: dosen">
          <script>
            $(document).ready(function() {
              $('#jabatan').on('input', function() {
                let val = $(this).val().toLowerCase(); // ubah jadi huruf kecil
                val = val.replace(/[^a-z]/g, ''); // hapus karakter selain a-z
                $(this).val(val);
              });
            });
          </script>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Nama Jabatan</label>
          <input type="text" name="nama_jabatan" id="nama_jabatan" class="form-control" required style="text-transform: capitalize;" placeholder="contoh: Dosen Tetap">
          <script>
            $(document).ready(function() {
              $('#nama_jabatan').on('input', function() {
                val = val.replace(/[^a-z ]/g, ''); // hapus karakter selain a-z
                $(this).val(val);
              });
            });
          </script>
        </div>
        <div class="col-md-6 mb-3">
          <label for="deskripsi" class="form-label">Deskripsi Pemberkasan pada Jabatan</label>
          <textarea rows="4" name="deskripsi" id="deskripsi" class="form-control" placeholder="contoh: Berkas untuk jabatan dosen semisal: Soal Ujian, Nilai Mahasiswa, dll"></textarea>
        </div>

      </div>
      <button type="submit" name="btn_tambah_jabatan" class="btn btn-success">
        <i class="bi bi-save"></i> Tambah Jabatan
      </button>
    </form>
  </div>
</div>