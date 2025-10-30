<?php
if (isset($_POST['btn_simpan_multiple'])) {
  foreach ($_POST['username'] as $key => $username) {
    if ($username) {
      echo "processing username: $username";
      $nama = $_POST[$key]['nama'];
      if (strlen($nama) >= 3) {
        echo 'on development...';
      } else {
        echo 'on development...';
      }

      echo "... OK<br>";
    }
  }


  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  exit;
}
