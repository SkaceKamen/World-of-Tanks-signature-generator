<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: wot_config.php
 *  Description: Config file
 *     
*/   

//Internal constant
define('WOT_CONFIG_LOADED', true);

//Load site url automatically
define('WOT_SITE_URL_AUTO', true);
//If auto is off, use this constant
define('WOT_SITE_URL', 'http://SITE_URL');

define('WOT_DEFAULT_LANGAUGE', 'en');
define('WOT_DEFAULT_SERVER', 'com');
define('WOT_DEFAULT_SETTINGS', '0111101');
define('WOT_DEFAULT_LINES', '110110001001001110110100110110110110100001');

//Use mod rewrite
define('WOT_MOD_REWRITE', true);
define('WOT_MOD_REWRITE_LINK', 'signature');
define('WOT_MOD_REWRITE_SEPARATOR', '@');

//Garage specific settings
define('WOT_GARAGE_ENABLED', true);
define('WOT_GARAGE_DEFAULT_SETTINGS', '0111101');
define('WOT_GARAGE_MOD_REWRITE', true);
define('WOT_GARAGE_MOD_REWRITE_LINK', 'garage');
define('WOT_GARAGE_MOD_REWRITE_SEPARATOR', '@');

//TRUE = cron is activated, FALSE = no cron, clean cache manually
define('WOT_CACHE_CRON', false);

define('WOT_ALLOW_CLAN_IMAGE', true);
//WIP, don't use!
define('WOT_ALLOW_MORE_PER_USER', true);
define('WOT_ALLOW_NOCACHE_PARAM', false);

//Folders
define('WOT_FOLDER_FUNCTIONS', 'wot_functions');
define('WOT_FOLDER_LANGUAGES', 'wot_languages');
define('WOT_FOLDER_MODULES', 'wot_modules');

define('WOT_FOLDER_DATA', 'wot_data');

define('WOT_FOLDER_BACKGROUNDS', 'wot_data/backgrounds');
define('WOT_FOLDER_FONTS', 'wot_data/fonts');
define('WOT_FOLDER_FLAGS', 'wot_data/flags');
define('WOT_FOLDER_IMAGES', 'wot_data/images');

define('WOT_FOLDER_BIG','wot_data/img');
define('WOT_FOLDER_SMALL', 'wot_data/imgSmall'); 
define('WOT_FOLDER_CONTUR', 'wot_data/imgContur');

define('WOT_FOLDER_CACHE_LINES', 'wot_data/cache_images');
define('WOT_FOLDER_CACHE_GARAGES', 'wot_data/cache_images');
define('WOT_FOLDER_CACHE_CLANS', 'wot_data/cache_images');
define('WOT_FOLDER_CACHE_JSON', 'wot_data/cache_json');

$wot_servers = array('eu', 'com', 'ru', 'sea');
?>