<?php
/*
  |--------------------------------------------------------------------------
  | Codekop PHP MVC Frameworks
  |--------------------------------------------------------------------------
  | is a simple open web project using php mvc frameworks 
  | simple php mvc made for the purpose of learning and new things about php 
  | with php mvc concept.
  | 
  | @package   : php-mvc-codekop
  | @author    : Codekop Dev Team
  | @copyright : Copyright (c) 2019-2020 Codekop.com (https://www.codekop.com)
  |
  | free for everyone to development a simple open web project using php mvc 
  | recommended php version for running is 7.0 but is run for php 5.6+
  | 
 */

if(phpversion() >= '5.6')
{
    error_reporting(0); // debug php dalam tahap pengembangan
    require_once 'system/init.php';
}else{
    echo 'Minimum php version for running is 5.6+, and Your php version is '.phpversion();
}
