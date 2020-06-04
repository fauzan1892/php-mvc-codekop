<?php namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Input method Settings ( Get & Post )
  |--------------------------------------------------------------------------
  |
 */

class Input {

    public function getPost($name, $t = null)
    {
        if($t == TRUE)
        {
            return strip_tags($_POST[''.$name.'']);
        }else{
            return $_POST[''.$name.''];
        }
    }

    public function getGet($name, $t = null)
    {
        if($t == TRUE)
        {
            return strip_tags($_GET[''.$name.'']);
        }else{
            return $_GET[''.$name.''];
        }
    }

}