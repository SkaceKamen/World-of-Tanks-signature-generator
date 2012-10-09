<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: wot_cron_daily.php
 *  Description: Script for clearing cache. Should be calle only once per day.
 *  Notice: If you don't have cron, you don't need to, just set WOT_CACHE_CRON to false and ignore this script 
 *     
*/ 
include_once('wot_config.php');

$folders = array(WOT_FOLDER_CACHE_LINES, WOT_FOLDER_CACHE_GARAGES, WOT_FOLDER_CACHE_CLANS, WOT_FOLDER_CACHE_JSON);
foreach($folders as $folder)
{
    $d = opendir($folder);
    while($s = readdir($d))
    {
        if ($s!='.' && $s!='..')
            unlink($folder . '/'.$s);
    }
}
?>