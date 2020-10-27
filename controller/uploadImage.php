<?php
  $imgData=file_get_contents($_POST['imgData']);
  $replace = [
    '/' => '',
    ':' => '',
    ' ' => '',
  ];
  $rand=str_replace(array_keys($replace),array_values($replace),date("Y/m/d H:i:s").mt_rand(100000000, 999999999999));
  $filename = $rand.'.jpg';
  file_put_contents('../uploads/' . $filename, $imgData);
  echo $rand.'.jpg';