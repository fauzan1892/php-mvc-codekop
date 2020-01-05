<?php

defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

    function redirect($url)
    {
        if($url)
        {
            return header('Location:'.base_url.$url);
        }else{
            return header('Location:'.base_url);
        }
    }

    function base_url($url)
    {
        return base_url.$url;
    }