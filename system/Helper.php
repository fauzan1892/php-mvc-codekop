<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Function Helpers default
  |--------------------------------------------------------------------------
  | 
  |
 */

   function redirect(?string $url = null): never
   {
      if($url)
      {
         // return header('Location:'.$url);
         header('Location: ' . filter_var($url, FILTER_SANITIZE_URL), true, 302);
         exit;
      }else{
         // return header('Location:'.base_url);
         header('Location: ' . base_url, true, 302);
         exit;
      }
   }

   function base_url(?string $url = null): string
   {
      return base_url.$url;
   }

   function e(mixed $value): string
   {
      return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
   }

   function csrf_field(): string
   {
      return '<input type="hidden" name="_csrf" value="' . e(\System\Security::token()) . '">';
   }

   function getSegments($segment)
   {
      $explode_url = explode('/',$_SERVER['REQUEST_URI']);
      $rowurl = count(explode('/',base_url));
      $row_url = count(parse_url(base_url));
      $urlc = $rowurl-$row_url;
      $rurl = $urlc-1;
      $cont = $segment + $rurl;
      $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
      return $uriSegments[$cont];
   }

    function page_rendered(): string
   {
      $start_time = microtime(TRUE);
      $end_time = microtime(TRUE);
      $time_taken =($end_time - $start_time)*1000;
      $time_taken = round($time_taken,5);
      
      return ''.$time_taken.'';
   }
