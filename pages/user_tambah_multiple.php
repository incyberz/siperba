<?php
harusAdmin();
include 'user_tambah_multiple-process.php';

?>

<style>
  /* Hanya berlaku untuk input dengan class form-control */
  .form-control:not(:placeholder-shown) {
    background-color: #d4edda !important;
    /* hijau lembut */
    border-color: #28a745 !important;
  }

  /* Opsional: efek fokus */
  .form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
  }
</style>


<form method="post" class="p-3">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Tambah Multi Users</h5>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th style="width: 5%;">No</th>
              <th>Username</th>
              <th>Nama Lengkap</th>
              <th>Nomor WhatsApp</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $baris = $_GET['baris'] ?? 10;
            for ($i = 1; $i <= $baris; $i++) {
              echo "
                <tr>
                  <td>$i</td>
                  <td>
                    <input type='text' name='username[]' class='input_username form-control' minlength='3' maxlength='20' placeholder='username...'>
                  </td>
                  <td>
                    <input type='text' name='nama[]' class='input_nama form-control' minlength='3' maxlength='30' placeholder='Nama lengkap...'>
                  </td>
                  <td>
                    <input type='text' name='whatsapp[]' class='form-control' minlength='11' maxlength='14' placeholder='opsional...'>
                  </td>
                </tr>
              ";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer text-end">
      <button type="submit" class="btn btn-primary" name="btn_simpan_multiple">
        <i class="bi bi-save"></i> Simpan Multi Users
      </button>
    </div>
  </div>
</form>

<script>
  $(function() {
    $(".input_username").on('input', function() {
      let val = $(this).val().toLowerCase(); // ubah jadi huruf kecil
      val = val.replace(/[^a-z]/g, ""); // hapus karakter selain a-z
      $(this).val(val);
    });
    $(".input_nama").on('input', function() {
      let val = $(this).val();
      val = val.replace(/["']/g, "`");
      val = val.replace(/[^a-zA-Z`., ]/g, "").replace(/\b\w/g, function(c) {
        return c.toUpperCase();
      });
      $(this).val(val);
    });
  });
</script>