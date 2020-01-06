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

    function uri_segments($segment)
    {
        $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        return $uriSegments[$segment];
    }

    function page_rendered()
    {
        $start_time = microtime(TRUE);
        $end_time = microtime(TRUE);
        $time_taken =($end_time - $start_time)*1000;
        $time_taken = round($time_taken,5);
         
        return ''.$time_taken.'';
    }