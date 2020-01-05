<?php

defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
/**
 * 
 * get post untuk mencegah sql injection
 * 
 */

    function param_get($get)
    {
        return strip_tags($_GET[$get]);
    }

    function param_post($post)
    {
        return strip_tags($_POST[$post]);
    }

/**
 * 
 * fungsi session start
 * 
 */

    function session_on()
    {
        if(!empty($_SESSION)){

        }else{
            $session = session_start();
        }

        return $session;
    }



?>