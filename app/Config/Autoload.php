<?php namespace Config;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Helper Settings
  |--------------------------------------------------------------------------
  | if your create new function for help your project,  for the solution about 
  | this your recommended for using with helper and fill
  | your file helper in the $helpers =  array();
  |
  | examples = $helpers = array('alert');
  | in alert on array() was taken in the folder app/Helper/alert.php
  |
 */

// fill the helper and helper required file $helpers = array('alert','fungsi','url');
$helpers = array('');

// for alert foreach included
foreach ($helpers as $helper)
{ 
    if($helper == null)
    {

    }else{
        include 'app/helper/'.$helper.'.php'; 
    }
} 

/*
  |--------------------------------------------------------------------------
  | Model Settings
  |--------------------------------------------------------------------------
  | if your create new class function for help your project,  for the solution about 
  | this your recommended for using with Model and fill
  | your file Model in the $models =  array();
  |
  | examples = $models =  array('Admin_model');
  | in Admin_model on array() was taken in the folder app/Models/Admin_model.php
  |
 */

$models = array('');

// for models foreach included
foreach ($models as $model)
{ 
    if($model == null)
    {

    }else{
        include 'app/Models/'.$model.'.php'; 
        $object = new $model();
    }
} 
