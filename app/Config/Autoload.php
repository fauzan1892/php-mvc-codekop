<?php defined('BASEPATH') OR exit('No direct script access allowed');
use System\Config;
$config = new \System\Config;
/*
  |--------------------------------------------------------------------------
  | Helper Settings
  |--------------------------------------------------------------------------
  | if your create new function for helping your project, for the solution about 
  | this your recommended for using with helper and fill your file helper in the array();
  |
  |
  | examples : $config::Helper(array('alert','fungsi','url'));
  | in alert on array() was taken in the folder app/Helper/alert.php
  |
 */
$config::Helper(array(''));

/*
  |--------------------------------------------------------------------------
  | Model Settings
  |--------------------------------------------------------------------------
  | if your create new class function for helping your project, for the solution about 
  | this your recommended for using with Model and fill your file Model in the array();
  | 
  |
  | examples : $config::Model(array('Admin_model'));
  | in Admin_model on array() was taken in the folder app/Models/Admin_model.php
  |
 */
$config::Model(array(''));
