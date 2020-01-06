<?php

defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
/**
 * 
 * get post untuk mencegah sql injection
 * 
 */

    function url_disallowed($param) {
        return preg_replace("/[^a-zA-Z0-9.]/", '', "{$param}");
    }

    function param_get($get)
    {
        return preg_replace("/[^a-zA-Z0-9.]/", '', "{$_GET[''.$get.'']}");
    }

    function param_post($post)
    {
        return preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST[''.$post.'']}");
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