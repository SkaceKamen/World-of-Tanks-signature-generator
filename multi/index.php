<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: index.php
 *  Description: Main script for both garage and lines generators settings, that include needed files and prepare HTML headers
 *     
*/

include_once('wot_config.php');
include_once(WOT_FOLDER_FUNCTIONS . '/get_language.php');
include_once(WOT_FOLDER_FUNCTIONS . '/clean.php');
include_once(WOT_FOLDER_FUNCTIONS . '/checked.php');
include_once(WOT_FOLDER_FUNCTIONS . '/selected.php');

/** GET LANGUAGE **/
$language = strtolower(get_language());

if (isset($_GET['jazyk']) && is_numeric($_GET['jazyk']) && $_GET['jazyk'] != '')
  $language = $_GET['jazyk'];

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

$POST = clean($_POST);
$GET = clean($_GET);

if (WOT_SITE_URL_AUTO)
{
    $url = parse_url($_SERVER['PHP_SELF']);
    $folder = explode('/', $url['path']);
    unset($folder[count($folder)-1]);
    $SITE_URL = 'http://' . $_SERVER['SERVER_NAME'] . implode('/', $folder);
} else {
    $SITE_URL = WOT_SITE_URL;
}
                   
ob_clean();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title>World of tanks Singature Creator</title>
  <link rel="stylesheet" href="<?php echo WOT_FOLDER_DATA?>/style.css" type="text/css">
  <script type="text/javascript" src="<?php echo WOT_FOLDER_DATA?>/cookies.js"></script>
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script type="text/javascript">
    var SITE_URL = "<?php echo $SITE_URL?>";
    var FOLDER_BACKGROUNDS = "<?php echo WOT_FOLDER_BACKGROUNDS?>";
    var FOLDER_IMAGES = "<?php echo WOT_FOLDER_IMAGES?>";
    var MOD_REWRITE = <?php echo WOT_MOD_REWRITE?>;
    var MOD_REWRITE_LINK = "<?php echo WOT_MOD_REWRITE_LINK?>"; 
    var MOD_REWRITE_SEPARATOR = "<?php echo WOT_MOD_REWRITE_SEPARATOR?>";
    var GARAGE_MOD_REWRITE = <?php echo WOT_GARAGE_MOD_REWRITE?>;
    var GARAGE_MOD_REWRITE_LINK = "<?php echo WOT_GARAGE_MOD_REWRITE_LINK?>";
    var GARAGE_MOD_REWRITE_SEPARATOR = "<?php echo WOT_GARAGE_MOD_REWRITE_SEPARATOR?>"; 
  </script>
  </head>
  <body>
    <div class="page">
      <div class="head">
        <div class="logo"><a href="wot_index.php"><img src="<?php echo WOT_FOLDER_IMAGES?>/logo.png" alt="World of tanks" border="0"></a></div>
        <?php if (WOT_GARAGE_ENABLED):?>
        <div class="menu">
          <a href="wot_index.php?type=0"<?php if (@$GET['type']!=1) echo ' class="selected"'?>><?php echo $translation['menu_lines']?></a>
          <a href="wot_index.php?type=1"<?php if (@$GET['type']==1) echo ' class="selected"'?>><?php echo $translation['menu_garage']?></a>
        </div>
        <?php endif;?>
      </div>
      <div class="main">
      <?php
      if (@$GET['type'] != 1 || !WOT_GARAGE_ENABLED)
          include_once('wot_index_lines.php');
      else
          include_once('wot_index_garage.php');
      ?>
      </div>
    </div>
  </body>
</html>