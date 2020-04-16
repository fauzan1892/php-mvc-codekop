<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !'); 
class Input {

    public function getPost($name, $t)
    {
        if($t == TRUE)
        {
            return preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST[''.$name.'']}");
        }else{
            return $_POST[''.$name.''];
        }
    }

    public function getGet($name, $t)
    {
        if($t == TRUE)
        {
            return preg_replace("/[^a-zA-Z0-9.]/", '', "{$_GET[''.$name.'']}");
        }else{
            return $_GET[''.$name.''];
        }
    }

}