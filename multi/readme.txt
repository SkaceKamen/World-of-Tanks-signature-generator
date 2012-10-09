WoT Signature generator
========================
Minimal requirements:
========================
  PHP 5+
  GD Library
  CUrl
  mod_rewrite (for .png adress. Not really needed)
  
========================
Notes:
========================
Configuration file is wot_config.php
If you don't want to use mod_rewrite change (in config) WOT_MOD_REWRITE to false.
If you can add cron tank, set wot_cron_daily.php to be executed once per day, otherwise change (in config) WOT_CACHE_CRON to false.

========================
Credits:
========================
Created (mostly) by SkaceKachna (zipek.cz,menxmenx@gmail.com)
Thanks to AgeofStrife for his scripts and showing me how to do some things.