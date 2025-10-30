<?php
function ekstensi_show($ekstensi_files)
{

  if (!$ekstensi_files) return null;
  $ekstensi_show = '';

  $icon_map = [
    'doc' => 'bi-file-earmark-word',
    'docx' => 'bi-file-earmark-word',
    'xls' => 'bi-file-earmark-excel',
    'xlsx' => 'bi-file-earmark-excel',
    'pdf' => 'bi-file-earmark-pdf',
    'zip' => 'bi-file-earmark-zip',
    'rar' => 'bi-file-earmark-zip',
    'ppt' => 'bi-file-earmark-ppt',
    'pptx' => 'bi-file-earmark-ppt',
    'txt' => 'bi-file-earmark-text',
  ];

  $ekstensi_list = explode(',', $ekstensi_files);

  foreach ($ekstensi_list as $ext) {
    $ext = strtolower(trim($ext));
    $icon = $icon_map[$ext] ?? 'bi-file-earmark'; // default generic icon
    $ekstensi_show .= "<span class='me-1' title='$ext'><i class='bi $icon'></i></span>";
  }
  return $ekstensi_show;
}
