<?php 
use System\Config;
$config = new \System\Config;
/*
  |--------------------------------------------------------------------------
  | BASE SITE URL
  |--------------------------------------------------------------------------
  | URL to your php mvc root. example :
  | https://codekop.com 
  |
  | or not changing your base_url, because we already used dynamic urls
  |
 */

  Config::$siteURL  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
  Config::$siteURL .= "://" . $_SERVER['HTTP_HOST'];
  Config::$siteURL .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

  /*
  |
  | Default Timezone
  |
  |
  |
  |
  */
  Config::$timeZone = 'Asia/Jakarta';
  Config::timeZone();
?>