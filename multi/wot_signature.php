<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: wot_signature.php
 *  Description: Script for creating statistics(line) picture
 *     
*/     

/** Load config **/
include_once('wot_config.php');
include_once(WOT_FOLDER_FUNCTIONS . '/get_language.php');
include_once(WOT_FOLDER_FUNCTIONS . '/sort_objects.php');
include_once(WOT_FOLDER_FUNCTIONS . '/clean.php');
include_once(WOT_FOLDER_MODULES . '/fetch.php');
include_once(WOT_FOLDER_MODULES . '/signature.php');

/** Clear input **/
$GET = clean($_GET);

/**
 * SETTINGS DEFINITION 
 *  0: Size of tanks
 *  1: How to order tanks
 *      0: By Battles
 *      1: By Wins
 *      2: By Tiers   
 *  2: Show tank name
 *  3: Show tank wins
 *  4: Show tank battles  
 *  5: Show tank percents of wins     
 *
 * LINES DEFINITION 
 * PER LINE:
 *   I+0: Show value
 *   I+1: Show position
 *   I+2: Show percentage      
**/

/** GET ALL ARGUMENTS **/
$player_id = @$GET['id'];
$player_server = @$GET['server'];
$player_flag = @$GET['flag'];
$background = @$GET['img'];
$image_size = @$GET['size'];
$font_size = @$GET['font'];
$lines = @$GET['lines'];
$settings = @$GET['settings'];

/** GET LANGUAGE **/
$language = strtolower(get_language());

if (isset($GET['lang']) && is_numeric($GET['lang']) && $GET['lang'] != '')
  $language = $GET['lang'];

/** SIMILIAR LANGUAGES **/
if ($language == 'cs')
    $language == 'sk';

/** LOAD LANGUAGE **/
if (file_exists(WOT_FOLDER_LANGUAGES . '/' . $language . '.php'))
{
    include_once(WOT_FOLDER_LANGUAGES . '/' . $language . '.php');
} else {
    include_once(WOT_FOLDER_LANGUAGES . '/' . WOT_DEFAULT_LANGAUGE . '.php');
}

/** INIT DEFAULT VALUES, IF NOT SET **/
if (!is_numeric($player_id))
{
    $player_id = explode('-',$player_id);
    $player_id = (int)$player_id[0];
}

if ($player_server == '') $player_server = WOT_DEFAULT_SERVER;
if ($background == '' || !is_numeric($background)) $background = 0;
if (!file_exists(WOT_FOLDER_BACKGROUNDS . '/wot' . $background . '.png') && $background != 0)  $background = 1;
if ($image_size != 0 && $image_size != 1) $image_size = 0;
if ($lines == '') $lines = WOT_DEFAULT_LINES;
if ($settings == '')  $settings = WOT_DEFAULT_SETTINGS;

$lines = str_split($lines);
$settings = str_split($settings);

$keys_all = array('integrated_rating', 'battles','battle_wins','losses','survived_battles','xp','battle_avg_xp','max_xp','ctf_points','dropped_ctf_points','damage_dealt','frags','spotted','hits_percents');

if (WOT_ALLOW_MORE_PER_USER)
    $filename = WOT_FOLDER_CACHE_LINES . '/' . $player_server.'_'.$player_id.date('d_m_y').'_'.$language_id.'_'.$image_size.'_'.$background.'_'.implode('',$lines).'_'.implode('',$settings).'_'.$player_flag.'_'.$font_size.'.png';
else
    $filename = WOT_FOLDER_CACHE_LINES . '/' . $player_server.'_'.$player_id.date('d_m_y').'_'.$language_id.'.png';

/** CHECK IF ID IS ENTERED **/
if ($player_id == '')
{
    $img = imagecreatefrompng(WOT_FOLDER_BACKGROUNDS . '/404.png');
}
else if (!file_exists($filename) || (WOT_ALLOW_NOCACHE_PARAM && @$GET['nocache']))  //If this signature isn't already cached
{
    $player = new Player($player_server, $player_id, WOT_FOLDER_CACHE_JSON);
    if (!$player->found)
    {
        //Generate 404 Error
        $img = imagecreatefrompng(WOT_FOLDER_BACKGROUNDS . '/404.png');
        $c_white = imagecolorallocate($img, 255, 255, 255);
        imagestring($img, 1, 0, imagesy($img) - 10, $player_id, $c_white);
    }
    else
    {
      $keys = array();
      
      for($i = 0; $i < count($keys_all); $i++)
      {
          if ($lines[($i * 3)] || $lines[($i * 3)+1] || $lines[($i * 3)+2])
          {
              $keys[$keys_all[$i]] = array($lines[($i * 3)], $lines[($i * 3)+1], $lines[($i * 3)+2]);
          }    
      }
      
      $signature = new Signature($image_size);
      $signature->background = $background;
      $signature->player = $player;
      $signature->keys = $keys;
      $signature->flag = $player_flag;
      $signature->font_size = $font_size;
      $signature->tanksize = $settings[0];
      $signature->tankorder = $settings[1];
      $signature->tankname = $settings[2];
      $signature->tankwins = $settings[3];
      $signature->tankbattles = $settings[4];
      $signature->tankpercentage = $settings[5]; 
      $signature->small_positions = $settings[6];
      $signature->server_label = $settings[7];
      $signature->effeciency_rating = $settings[8];
      $signature->clan_name = $settings[9];
      $signature->clan_image = $settings[10];
      
      $img = $signature->render();
      //Save image to cache
      imagepng($img, $filename);
      
      //Clear cache, if it wasn't cleared today
      if (!WOT_CACHE_CRON)
      {
          if (!file_exists(WOT_FOLDER_CACHE_LINES . '/cache'.date('d_m_y')))
          {
              $d = opendir(WOT_FOLDER_CACHE_LINES);
              while($s = readdir($d))
              {
                  if ($s!='.' && $s!='..')
                      if (strpos($s,date('d_m_y')) == 0)
                          unlink(WOT_FOLDER_CACHE_LINES . '/'.$s);
              }
              $f = fopen(WOT_FOLDER_CACHE_LINES . '/cache'.date('d_m_y'),'w');
              fclose($f);
          }
          if (!file_exists(WOT_FOLDER_CACHE_CLANS . '/cache'.date('d_m_y')))
          {
              $d = opendir(WOT_FOLDER_CACHE_CLANS);
              while($s = readdir($d))
              {
                  if ($s!='.' && $s!='..')
                      if (strpos($s,date('d_m_y')) == 0)
                          unlink(WOT_FOLDER_CACHE_CLANS . '/'.$s);
              }
              $f = fopen(WOT_FOLDER_CACHE_CLANS . '/cache'.date('d_m_y'),'w');
              fclose($f);
          }
          if (!file_exists(WOT_FOLDER_CACHE_JSON . '/cache'.date('d_m_y')))
          {
              $d = opendir(WOT_FOLDER_CACHE_JSON);
              while($s = readdir($d))
              {
                  if ($s!='.' && $s!='..')
                      if (strpos($s,date('d_m_y')) == 0)
                          unlink(WOT_FOLDER_CACHE_JSON . '/'.$s);
              }
              $f = fopen(WOT_FOLDER_CACHE_JSON . '/cache'.date('d_m_y'),'w');
              fclose($f);
          }
      }
    }
} else {
    $img = imagecreatefrompng($filename);
    imagealphablending($img, true);
    imagesavealpha($img, true);
}

ob_clean();
header('Content-type: image/png');
imagepng($img);
imagedestroy($img);
?>                  