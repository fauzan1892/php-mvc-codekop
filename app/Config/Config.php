<?php
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


    define('base_url',"http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/');
    define('BASEPATH', base_url);
    defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

/*
  |--------------------------------------------------------------------------
  | DEFAULT CONTROLLER USED
  |--------------------------------------------------------------------------
  | this is a controller default used for your main page urls 
  |
 */
    define('default_controller','Home');
?>