<?php

$font = "include/VeraSeBd.ttf";
$fontsize = 18;
$signature = "lanaicmslanaicmslanaicmslanaicmslanaicms";

session_start(); 

// generate  5 letter random string
$rand = "";

// some easy-to-confuse letters taken out C/G I/l Q/O h/b 
$letters = "ABDEFHKLMNOPRSTUVWXZabdeghikmnoprsuvwxyz";
for ($i = 0; $i < 5; ++$i) {
  $rand .= substr($letters, rand(0,strlen($letters)-1), 1);
 }
 
 //$rand = strtolower($rand);

 $_SESSION['captcha'] = $rand;

$imgwid = 110;
$imghgt = 40;

// Set the content-type
header("Content-type: image/png");

// Create the image
$im = imagecreatetruecolor($imgwid, $imghgt);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 150, 150, 150);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, $imgwid, $imghgt, $white);

 $cmtcol = imagecolorallocatealpha ($im, 180, 180, 180, 0);
  for ($i=0;$i<10;$i++){
  imagestring($im, 1, 1, ($i*5), $signature, $cmtcol);
  }
  
  imagerectangle($im, 0, 0, $imgwid-1, $imghgt-1, $black);
  

// Add some shadow to the text
imagettftext($im, $fontsize,3, 6, 30, $grey, $font, $rand);

$color = imagecolorallocate($im, rand(100,200), rand(100,200), rand(100,200));

// Add the text
imagettftext($im, $fontsize, 3, 6, 31, $color, $font, $rand);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>