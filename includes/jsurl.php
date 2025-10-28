<?php
function jsurl($url = null, $milidetik = 0)
{ // v1.2 
  if (!$url) {
    $arr = explode('?', $_SERVER['REQUEST_URI']);
    jsurl("?$arr[1]", $milidetik);
    exit;
  }
  echo "
    <div class='consolas f12 abu'>Please wait, redirecting in $milidetik mili seconds...</div>
    <script>
      setTimeout(()=>{
        location.replace('$url');
      },$milidetik);
    </script>
  ";
  exit;
}

function jsreload()
{
  die('<script>location.reload()</script>');
}
