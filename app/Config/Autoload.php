<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
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