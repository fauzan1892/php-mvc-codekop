<?php namespace Config;
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
  $config['base_url'] = "http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';
 

?>