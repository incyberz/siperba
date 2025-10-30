<?php
session_start();
include '../conn.php';
include '../includes/harusAdmin.php';

harusAdmin();

$whatsapp = $_GET['whatsapp'] ?? die('index whatsapp invalid.');
$user_id = $_GET['user_id'] ?? die('index user_id invalid.');
$set_null = $_GET['set_null'] ?? null;

if ($set_null && $whatsapp) die("Parameter aksi Set Null invalid. $set_null");

$whatsapp = $set_null ? 'NULL' : "'$whatsapp'";

$s = "UPDATE tb_user SET whatsapp = $whatsapp WHERE id = '$user_id'";
// die($s);
$q = mysqli_query($conn, $s) or die(mysqli_error($conn));
die('OK');
