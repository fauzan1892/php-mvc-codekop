<?php
/*
  |--------------------------------------------------------------------------
  | Function Helpers default
  |--------------------------------------------------------------------------
  | 
  |
 */

   function redirect($url)
   {
      if($url)
      {
         return header('Location:'.$url);
      }else{
         return header('Location:'.base_url);
      }
   }

   function base_url($url)
   {
      return base_url.$url;
   }

   function getSegments($segment)
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
