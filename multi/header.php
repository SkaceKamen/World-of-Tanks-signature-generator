<?php
  $ip = $_SERVER["REMOTE_ADDR"];
  include("geoip.inc");
  include("fnc_getLanguage.php");
      
  $lang = strtolower(get_language());
  if ($lang == "")
  {
    $gi = geoip_open("GeoIP.dat",GEOIP_STANDARD);
    $zeme = geoip_country_code_by_addr($gi, $ip);
    switch($zeme)
    {
      case "CZ": case "SK": $jazyk = 0; break;
      default: $jazyk = 0; break;
    }
    
  } else {
    switch($lang)
    {
      case "cs": case "sk": $jazyk = 1; break;
      default: $jazyk = 0; break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title>World of tanks Singature Creator</title>
  <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
  
    <div class="page">
      <div class="head"><a href="index.php"><img src="WoTLogo.png" alt="World of tanks" border="0"></a></div>
      <div class="main">