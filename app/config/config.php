<?php
/**
 * 
 * berisi define-define kebutuhan website
 */


    define('base_url',"http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/');
    
    define('BASEPATH', base_url);
    
    define('default_view','welcome');
    
    defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

?>