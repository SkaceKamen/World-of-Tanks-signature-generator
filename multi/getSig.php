<?php
$sigCount = 109;

$sigCat = array(
  -1,
  4,
  2,
  2,
  2,
  1,
  1,
  1,
  1,
  1,
  1,
  1,
  1,
  1,
  1,
  2,
  2,
  2,
  2,
  1,
  1,
  3,
  3,
  3,
  3,
  3,
  3,
  5,
  5,
  5,
  5,
  5,
  5,
  5,
  5,
  5,
  5,
  5,
  4,
  5
);

for($i = 40; $i <= 103; $i+=1)
  $sigCat[] = 1;
  
$sigCat[] = 2;
for($i = 105; $i <= 109; $i+=1)
  $sigCat[] = 4;

$cat = (int)$_GET["catid"];

foreach($sigCat as $key=>$value)
  if (($value == $cat || $cat == 0) && $value!=-1)
  {
    echo "<div class='singature_float'><img src='sig/wot$key.png' alt='' onclick='sig_selected($key)'><div class='check'><input type='radio' id='img$key' name='img' value='$key' "; if ($key == $_COOKIE["LastImg"]) echo " checked='true'"; echo "></div></div>";
  }
  
echo "<div class='clear'></div>";
?>