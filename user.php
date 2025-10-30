<?php
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username'"));
$isDefaultPass = md5($user['username']) === $user['password'];
$role = $user['role'];
