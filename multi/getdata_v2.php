<?php

/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 */   


/** DECLARE FUNCTIONS **/

function imagettftextoutline($image,$size,$angle,$x,$y,$color,$fontfile,$text,$outlinewidth = 1,$outlinecolor = 0)
{
	imagettftext($image, $size, $angle, $x - $outlinewidth,$y - $outlinewidth, $outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x - $outlinewidth,$y + $outlinewidth, $outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x + $outlinewidth,$y - $outlinewidth, $outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x + $outlinewidth,$y + $outlinewidth, $outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x - $outlinewidth, $y,$outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x + $outlinewidth, $y,$outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x, $y - $outlinewidth,$outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x, $y + $outlinewidth,$outlinecolor, $fontfile, $text);
	imagettftext($image, $size, $angle, $x, $y, $color, $fontfile,$text);
}

function get_tier($str)
{
  $str = trim($str);
  switch($str)
  {
    case 'X': return 10; break;
    case 'IX': return 9; break;
    case 'VIII': return 8; break;
    case 'VII': return 7; break;
    case 'VI': return 6; break;
    case 'V': return 5; break;
    case 'IV': return 4; break;
    case 'III': return 3; break;
    case 'II': return 2; break;
    case 'I': return 1; break;
  }
  return 0;
}

function get_part($str,$from,$to)
{
  $value = substr($str,strpos($str,$from)+strlen($from),300);
  $value = substr($value,0,strpos($value,$to));
  return $value;    
}



foreach($_GET as $key=>$value) { $_GET[$key] = trim(htmlspecialchars($value)); $_GET[$key] = str_replace("'",'',$_GET[$key]); }

$ip = $_SERVER['REMOTE_ADDR'];

/** GET ALL ARGUMENTS **/

$id = $_GET['id'];
$server = $_GET['server'];
$sig = $_GET['img'];
$size = $_GET['size'];
$advanced = $_GET['advanced'];
$advanced_ext = $_GET['advanced_ext'];
$settings = $_GET['settings'];
$flag = $_GET['flag'];
$hsize = $_GET['font'];

/** GET LANGUAGE 
    Language is stored in $jazyk
    0 = CZ
    1 = EN
    2 = RU
**/

include('fnc_getLanguage.php');

$lang = strtolower(get_language());
switch($lang)
{
  case 'cs': case 'sk': $jazyk = 0; break;
  case 'ru': $jazyk = 2; break;
  default: $jazyk = 1; break;
}

/** INIT DEFAULT VALUES, IF NOT SET **/

if (!is_numeric($id))
{
  $id = explode('-',$id);
  $id = (int)$id[0];
}

if ($server == '') $server = 'com';
if ($sig == '' || !is_numeric($sig)) $sig = 1;
if (!file_exists("sig/wot$sig.png"))  $sig = 1;
if ($size != 0 && $size != 1) $size = 0;
if ($advanced == '') $advanced = '1111111111111';
if ($advanced_ext == '') $advanced_ext = '11111111111111';
if ($settings == '')  $settings = '00101';

$adv = $advanced;
$adv2 = $advanced_ext;
$set = $settings;
$advanced = str_split($advanced);
$advanced_ext = str_split($advanced_ext);
$settings = str_split($settings);
$tanksize = $settings[0];
$tankorder = $settings[1];
$positionSmall = $settings[2];
$tankname = $settings[3];
$tanknumbers = $settings[4];

if (is_numeric($_GET['jazyk']) && $_GET['jazyk'] != "")
  $jazyk = $_GET['jazyk'];

/** TRANSLATION OF TEXTS **/
$preklad = array(
  array(
    array('Bitev','Vítězství','Porážek','Přezito bitev','Zkušeností','Průměr zkuš. na bitvu','Nejvíc zkuš. za bitvu',
          'Obsazení základny','Obrana základny','Poškození','Zničeno','Detekováno','Přesnost'),
    array('Celková pozice')
  ),
  array(
    array('Battles','Victories','Defeats','Battles Survived','Total Experience','Avg Experience per Battle','Max Experience per Battle',
          'Base Capture','Base Defense','Damage','Destroyed','Detected','Hit Ratio'),
    array('Global Position')
  ),
  array(
    array('Проведено боёв','Побед','Проигрышей','Выжил в битвах','Суммарный опыт','Средний опыт за бой','Максимальный опыт за бой',
          'Очки захвата базы','Очки защиты базы','Нанесенные повреждения','Уничтожено','Обнаружено','Процент попадания'),
    array('Общий Место')
  ),
);

$filename = 'cache/'.$id.date("d_m_y").'_'.$size.'_'.$sig.'_'.$jazyk.'_'.$adv.'_'.$adv2.'_'.$set.'_'.$flag.'_'.$hsize.'.png';

/** CHECK IF ID IS ENTERED **/
if ($id=='')
{
  $img = imagecreatefrompng("sig/WoTPrimary404.png");
}
else if (!file_exists($filename))  //If this signature isn't already cached
{

  /** GET URL, RU AND US SERVER HAS DIFFERENT **/
  switch(strtolower($server))
  {
    case 'ru': $url="http://challenge.worldoftanks.ru/uc/accounts/$id/"; break;
    case 'sea': $url = "http://worldoftanks-sea.com/community/accounts/$id/"; break;
    default: $url="http://worldoftanks.$server/community/accounts/$id/"; break;
  }
  
  /** DOWNLOAD DATA **/
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  $string = curl_exec($ch);
  curl_close($ch);
  
  /** GET LOGIN **/
  $login = get_part($string,'<h1>','</h1>');
  
  /** WAS PROFIE FOUND? **/
  if ($string == "" || $login == "" || $login == ' PAGE NOT FOUND ')
  {
    //Generate 404 Error
    $img = imagecreatefrompng("sig/WoTPrimary404.png");
    $c_white = imagecolorallocate($img, 255, 255, 255);
    imagestring($img, 1, 0, imagesy($img) - 10, $id, $c_white);
  }
  else
  {
    //IF USER HAS CLAN
    $s = '<a class="b-link-clan"';
    if (strpos($string,$s)!=0)
    {
      $clan = substr($string,strpos($string,$s)+strlen($s),300);
      $clan = substr($clan,strpos($clan,'[')+1,strpos($clan,']')-strpos($clan,'[')-1);
      $login = "[$clan] $login";
    }
  
    /** 
     GET ITEMS
    **/
    
    //Global rating is located elsewhere, than the rest of items
    if ($server == 'ru')
      $items = array('Общий рейтинг');
    else
      $items = array('Global Rating');
    
    $result = array();  
    $history = array();
    foreach($items as $key => $item)
    {
      //First, get the whole line
      $str = get_part($string, '<td><span>'.$item.'</span></td>', '</tr>');
      //Eliminate value, we want only position
      $str2 = get_part($str, '<td class="right value">','</td>');
      $str = str_replace('<td class="right value">'.$str2.'</td>','',$str);
      $str = get_part($str, '<td class="right value">','</td>');
      //Remove link, if it's present
      if (strpos($str, '>'))
        $str = get_part($str,'>','<');
      //Remove spaces
      $str = str_replace(' ','',$str);
      $value = str_replace('&nbsp;',' ',$str);
      $value .= '.';
      $item = $preklad[$jazyk][1][$key];
      if ($advanced[0] == 1)
        $result[] = array($item,$value);
    }
      
    //Now get rest of the items
    //Alias is alias used to get position
    if ($server == 'ru') {
      $items = array('Проведено боёв','Побед','Проигрышей','Выжил в битвах','Суммарный опыт','Средний опыт за бой','Максимальный опыт за бой',
                     'Очки захвата базы','Очки защиты базы','Нанесенные повреждения','Уничтожено','Обнаружено','Процент попадания');
      $aliases = array('Проведено боёв','Побед','Проигрышей','Выжил в битвах','Суммарный опыт','Средний опыт за бой','Максимальный опыт за бой',
                     'Захват базы','Защита базы','Нанесенные повреждения','Уничтожено врагов','Обнаружено врагов','Процент попадания');
    } else {
      $items = array('Battles Participated','Victories','Defeats','Battles Survived','Total Experience','Average Experience per Battle','Maximum Experience per Battle',
                     'Capture Points','Defense Points','Damage','Destroyed','Detected','Hit Ratio');
      $aliases = array('Battles Participated','Victories','Defeats','Battles Survived','Total Experience','Average Experience per Battle','Maximum Experience per Battle',
                     'Capture Points','Defense Points','Damage','Targets Destroyed','Targets Detected','Hit Ratio');
    }
    foreach($items as $key=>$item)
    {
      //Get line
      $str = get_part($string,'<td class=""> '.$item.': </td>','</tr>');
  
      $str = get_part($str,'<td class="td-number-nowidth">','</td>');
      
      //Replace spaces
      $str = str_replace('&nbsp;',' ',$str);
      //It has %, replace the original value with %
      if (strpos($str,'(')) {
        $str = get_part($str,'(',')');
      }
      //If item has position!
      if (strpos($string,'<td><span>'.$aliases[$key]) && $advanced_ext[$key] == '1')
      {
        $str2 = get_part($string,'<td><span>'.$aliases[$key],'</tr>');

        $_str = get_part($str2, '<td class="right value">', '</td>');
        $str2 = str_replace('<td class="right value">'.$_str.'</td>','',$str2);

        $str2 = str_replace(' ','',$str2);
        $str2 = trim(get_part($str2, '<tdclass="rightvalue">', '</td>'));
        if (strpos($str2, '>'))
          $str2 = get_part($str2,'>','<');

        $str2 = str_replace('&nbsp;',' ',$str2);
        $str2 .= '.';
      } else {
        $str2 = '';
      }                                                           
      //Get translated item name
      $item = $preklad[$jazyk][0][$key];
      //Store item (If it suppose to be stored) 
      if ($advanced[1+$key] == 1)
        $result[] = array($item,trim($str),$str2);
      else if ($advanced_ext[$key] == 1 && $str2!='')
        $result[] = array($item,trim($str2), '');
    }
    
    /**
     GET TANKS
     **/ 

    //Get entire table
    $s = '<table  class="t-statistic">';
    $tanky = substr($string,strpos($string,$s)+strlen($s),strlen($string));
    $tanky = substr($tanky,0,strpos($tanky,'</table>'));
    
    //Split string on table rows
    $radky = explode('<tr>',$tanky);
    $tanky = array();
    foreach($radky as $str)
    {
      //Get tank name
      $nazev = trim(strip_tags(get_part($str,'<td class="value">','</td>')));
      
      //Get tank image
      $img = get_part($str,'<img class="png" src="','"');
      $img = substr($img,1,strlen($img));
      while(strpos($img,'/')!=0)
        $img = substr($img,strpos($img,'/')+1,strlen($img));
      
      //Get tank background  
      if (strpos($str,'js-usa td-armory-icon')!=0)
        $icon = 'usa.png';
      if (strpos($str,'js-germany td-armory-icon')!=0)
        $icon = 'germany.png';
      if (strpos($str,'js-ussr td-armory-icon')!=0)
        $icon = 'ussr.png';
      if (strpos($str,'js-france td-armory-icon')!=0)
        $icon = 'france.png';
      
      //Get rest of data
      $tier = trim(strip_tags(get_part($str,'<span class="level">','</span>')));  
      $bitev = get_part($str,'<td class="right value">','</td>');  //Battles
      $str = get_part($str,'<td class="right value">','</tr>');
      $vitezstvi = get_part($str,'<td class="right value">','</td>'); //Wins

      $nazev = iconv('UTF-8', 'Latin2', $nazev); //Encode tank name to Latin2 (used by imagestring)
      
      if ($nazev!='')
        $tanky[] = array('nazev'=>$nazev,'img'=>$img,'bitev'=>$bitev,'vitezstvi'=>$vitezstvi,'icon'=>$icon,'tier'=>get_tier($tier));
    }
    
    //Sort tanks by tier
    if ($tankorder)
    {
      function tanky_comp($a, $b)
      {
        if ($a['tier'] > $b['tier'])
          return false;
        else
          return true;
      }
      usort($tanky, 'tanky_comp');
    }
    
    //Get sig size
    switch($size)
    {
      case 0: $width = 400; $height = 100; break;
      case 1: $width = 512; $height = 128; break;
    }
    
    //Load empty png file (to avoid some alpha blending problems)
    $img = imagecreatefrompng("sig/wot_empty_$size.png");
    imagealphablending($img, true);
    imagesavealpha($img, true);
    
    //Allocate colors
    $c_white = imagecolorallocate($img, 255, 255, 255);
    $c_gray = imagecolorallocate($img, 200, 200, 200);
    $c_black = imagecolorallocate($img, 0, 0, 0);
    $c_purple = imagecolorallocate($img, 254, 254, 254);
    
    //Load background
    $bg = imagecreatefrompng("sig/wot$sig.png");
    //This image is used to dark image
    $tm = imagecreatefrompng("sig/wot_black.png");
    
    //Copy background and dark image to the signature
    imagecopyresampled($img, $bg, 0, 0, 0, 0, $width, $height, 512, 128);
    imagecopyresized($img, $tm, 0, 0, 0, 0, $width, $height, 512, 128);
    
    //Get font properties, by signature size 
    switch($size)
    {
      case 0: $titlesize = 14; $fontsize = 8; $font = "fonty/CALIBRI.TTF"; break;
      case 1: $titlesize = 16; $fontsize = 10; $font = "fonty/CALIBRI.TTF"; break;
    }
    
    //or use custom size
    if ($hsize!=0) {
      $fontsize = $hsize;
    }
    
    //Get server name
    switch($server)
    {
      case 'eu': $text = 'EU server'; break;
      case 'com': $text = 'NA server'; break;
      case 'ru': $text = 'RU server'; break;
    }
    
    //Draw server name
    $box = imagettfbbox($fontsize, 0, $font, $text);
    imagettftextoutline($img, $fontsize, 0, $width - $box[2] - 8, 4 - $box[7], $c_white, $font, $text, 1, $c_black);
    
    $fontB = "fonty/ARIALBD.TTF";
    $box = imagettfbbox($titlesize, 0, $fontB, $login);
    $xx = 8;
    
    //Draw flag
    if ($flag!="" && file_exists("flags/$flag.gif"))
    {
      $flagi = imagecreatefromgif("flags/$flag.gif");
      imagecopy($img, $flagi, $xx, 6 + $size, 0, 0, imagesx($flagi), imagesy($flagi));
      $xx += 20;
    }
    
    //Draw nick
    imagettftextoutline($img, $titlesize, 0, $xx, 4 - $box[7], $c_white, $fontB, $login, 1, $c_black);
    
    //TH is height of title
    $th = -$box[7];
    
    switch($tanksize)
    {
        case 0:
          $tankx = 10;
          $tankwidth = 55;
          $tankheight = 31;
          $tanksrcwidth = 55;
          $tanksrcheight = 31;
          $fld = "imgSmall";
        break;
        case 1:
          $tankx = 19;
          $tankwidth = 86;
          $tankheight = 66;
          $tanksrcwidth = 130;
          $tanksrcheight = 100; 
          $fld = "img";
        break;
        case 3:
          $tankx = 0;
          $tankwidth = 84;
          $tankheight = 24;
          $tanksrcwidth = 84;
          $tanksrcheight = 24; 
          $fld = "imgContur";
        break;
    }
    
    function addEnd($str)
    {
      global $line, $positionSmall;
      
      if ($line[2]!='')
        if (($positionSmall && (int)(str_replace(' ','',$line[2])) < 1000) || !$positionSmall)
          return $str . ' ('.$line[2].')';
      return $str;
    }
    
    //Now, find out, if the lines can fit on more cols, to leave more space for tanks
    $max_width = 0;
    $xx = 8;
    $yy = 4 - $box[7] + 2;
    $sy = $yy;
    $sx = $xx;

    $fail = false;
    foreach($result as $line)
    {
      //Should we move to next col?
      if ($yy - $box[7] + 4 > $height - $tankheight - 4)
      {
        $xx += $max_width + 4; //Add xx
        $yy = 4 + $th + 2; //Reset YY
        $max_width = 0;    //Reset max width of col
      }
      
      $str = addEnd("$line[0]: $line[1]"); //Get str
      $box = imagettfbbox($fontsize, 0, $font, $str); //Get str size

      $w = $box[2]; //Line width
      if ($w > $max_width)
      {
        $max_width = $w;
        if ($xx + $max_width > $width-4) //If is max width over signature size, we cant fit lines to more cols...
        {
          $fail = true;
          break;
        }
      }
      $yy += -$box[7] + 1 + 1 * $size; //Move to next line
    }
    
    if ($fail) //If the check failed
      $maxheight = $height;
    else
      $maxheight = $height - $tankheight - 4; 
    
    //Now draw value names
    
    $xx = $sx;
    $yy = $sy;
    $col = 0;
    $max_width = array();
    $max_width[$col] = 0;  
    
    foreach($result as $key=>$line)
    {
      if ($yy - $box[7] + 4 > $maxheight)
      {
        $xx += $max_width[$col] + 5;
        $yy = 4 + $th + 2;
        $col+=1;
        $max_width[$col] = 0;
      }
      $h[$key] = 0;
      $str = "$line[0]: ";
      $box = imagettfbbox($fontsize, 0, $font, $str);
      imagettftextoutline($img, $fontsize, 0, $xx, $yy - $box[7], $c_gray, $font, $str, 1, $c_black);
      $h[$key] = -$box[7];
      $str = addEnd("$line[0]: $line[1]");
      $box = imagettfbbox($fontsize, 0, $font, $str);
      $w = $box[2];
      
      if ($w > $max_width[$col])
        $max_width[$col] = $w;
      $yy += $h[$key] + 1 + 1 * $size;
    }
    
    //Now draw values
    
    $xx = $sx;
    $yy = $sy;
    $col = 0;
    $lasth = 0;
    
    foreach($result as $key=>$line)
    {
      if ($yy + $lasth + 4 > $maxheight)
      {
        $xx += $max_width[$col] + 5;
        $col += 1;
        $yy = 4 + $th + 2;
      }

      $str = addEnd("$line[1]");
      $box = imagettfbbox($fontsize, 0, $font, $str);
      imagettftextoutline($img, $fontsize, 0, $xx + $max_width[$col] - $box[2], $yy + $h[$key], $c_white, $font, $str, 1, $c_black);
      
      $yy += $h[$key] + 1 + 1 * $size;
      $lasth = $h[$key];
    }
    $yy -= $lasth + 1 + 1 * $size;

    /**
      Draw tanks
    **/

    if ($tanksize != 2)
    {
      //Allocate color of background
      $c_awhite = imagecolorallocatealpha($img, 0, 0, 0, 80);
      
      //Can tanks fit to last col, or move to next col
      $ly = $yy;
      $yy = $height - $tankheight - 4;
      if ($ly > $yy)                            
        $xx += $max_width[count($max_width)-1];
      if ($fail)
        $minx = $xx;
      else
        $minx = 8;
        
      //Calculate starting xx
      $xx = $width - 4 - floor(($width - $minx - 4) / ($tankwidth + 4)) * ($tankwidth + 4);
      $first = true;
      
            foreach($tanky as $tank)
      {
        //Does this tank fit to siganture?
        if ($xx + $tankwidth + 4 < $width)
        {
          //Draw info for first tank
          if ($first)
          {
            $tank['bitev'] = 'B:'.$tank['bitev'];
            $tank['vitezstvi'] = 'W:'.$tank['vitezstvi'];
            $first = false;
          }
        
          //Load tank image
          $tk = imagecreatefrompng("$fld/$tank[img]");
          //Draw tank background
          imagefilledrectangle($img, $xx, $yy, $xx+$tankwidth-1, $yy+$tankheight-1, $c_awhite);
          if ($tanksize == 0)
          {
            //Draw special tank background
            if ($tank['icon']!='')
            {
              $bg = imagecreatefrompng("imgSmall/$tank[icon]");
              imagecopy($img, $bg, $xx, $yy, 0, 0, $tankwidth, 31);
            }
          }
          //Draw tank image
          imagecopyresampled($img, $tk, $xx, $yy, $tankx, 0, $tankwidth, $tankheight, $tanksrcwidth, $tanksrcheight);
          
          //Draw tank name
          if ($tankname == 1)
          {
            $nazev = $tank['nazev'];
            if (strlen($nazev) > floor($tankwidth/imagefontwidth(1)))
              $nazev = substr($nazev,0,floor($tankwidth/imagefontwidth(1)));
            imagestring($img, 1, $xx, $yy, $nazev, $c_white);
          }
          //Draw tank battles/wins count
          if ($tanknumbers == 1)
          {
            imagestring($img, 1, $xx, $yy + $tankheight - imagefontheight(1), $tank["bitev"], $c_white);
            imagestring($img, 1, $xx + $tankwidth - strlen($tank["vitezstvi"]) * imagefontwidth(1), $yy + $tankheight - imagefontheight(1), $tank["vitezstvi"], $c_white);
          }
          //Move to next tank
          $xx += $tankwidth + 4;
        } else break;
      }
    }
    
    
    //We don't want to have more versions from same user
    /*$d = opendir("cache/");
    while($s = readdir($d))
    {
      if ($s!="." && $s!="..")
      {
        if (preg_match('/'.$id.'_(.*)_'.$jazyk.'\.png/', $s))
          unlink("cache/$s");
      }
    }*/
    
    //Save image to cache
    imagepng($img, $filename);
    
    //Clear cache, if it wasn't cleared today
    if (!file_exists('cache/cache'.date('d_m_y')))
    {
      $d = opendir('cache/');
      while($s = readdir($d))
      {
        if ($s!='.' && $s!='..')
          if (strpos($s,date('d_m_y')) == 0)
            unlink('cache/'.$s);
      }
      $f = fopen('cache/cache'.date('d_m_y'),'w');
      fclose($f);
    }
  }
} else {
  $img = imagecreatefrompng($filename);
  imagealphablending($img, true);
  imagesavealpha($img, true);
}

header('Content-type: image/png');
imagepng($img);
imagedestroy($img);
?>                  