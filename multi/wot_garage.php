<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: wot_garage.php
 *  Description: Script for generating garage pictures
 *     
 */   

/** Load config **/
include_once('wot_config.php');
include_once(WOT_FOLDER_FUNCTIONS . '/get_language.php');
include_once(WOT_FOLDER_FUNCTIONS . '/sort_objects.php');
include_once(WOT_FOLDER_FUNCTIONS . '/clean.php');
include_once(WOT_FOLDER_MODULES . '/fetch.php');
include_once(WOT_FOLDER_MODULES . '/garage.php');

/** Exit if garage is forbidden **/
if (!WOT_GARAGE_ENABLED)
    exit;

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
$image_size = @$GET['size'];
$settings = @$GET['settings'];

if ($settings == '')  $settings = WOT_GARAGE_DEFAULT_SETTINGS;

$settings = str_split($settings);

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

if (WOT_ALLOW_MORE_PER_USER)
    $filename = WOT_FOLDER_CACHE_GARAGES . '/garage_' . $player_server.'_'.$player_id.date('d_m_y').'_'.$player_flag.'_'.'_'.$image_size.'_'.implode('',$settings).'_'.$language_id.'.png';
else
    $filename = WOT_FOLDER_CACHE_GARAGES . '/garage_' . $player_server.'_'.$player_id.date('d_m_y').'_'.$language_id.'.png';

/** CHECK IF ID IS ENTERED **/
if ($player_id == '')
{
    $img = imagecreatefrompng(WOT_FOLDER_BACKGROUNDS . '/404.png');
}
else if (!file_exists($filename) || (WOT_ALLOW_NOCACHE_PARAM && @$GET['nocache']))
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

      $garage = new Garage($image_size);
      $garage->player = $player;
      $garage->flag = $player_flag;
      $garage->tanksize = $settings[0];
      $garage->tankorder = $settings[1];
      $garage->tankname = $settings[2];
      $garage->tankwins = $settings[3];
      $garage->tankbattles = $settings[4];
      $garage->tankpercentage = $settings[5];
      
      $img = $garage->render();
      //Save image to cache
      imagepng($img, $filename);
      
      //Clear cache, if it wasn't cleared today
      if (!WOT_CACHE_CRON)
      {
          if (!file_exists(WOT_FOLDER_CACHE_GARAGES . '/cache'.date('d_m_y')))
          {
              $d = opendir(WOT_FOLDER_CACHE_GARAGES);
              while($s = readdir($d))
              {
                  if ($s!='.' && $s!='..')
                      if (strpos($s,date('d_m_y')) == 0)
                          unlink(WOT_FOLDER_CACHE_GARAGES . '/'.$s);
              }
              $f = fopen(WOT_FOLDER_CACHE_GARAGES . '/cache'.date('d_m_y'),'w');
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