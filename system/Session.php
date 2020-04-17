<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !'); 
/*
  |--------------------------------------------------------------------------
  | Session Settings
  |--------------------------------------------------------------------------
  |
 */

class Session {

    public function __construct()
    {
        $default = 'codekop_session';
        if(!empty($_SESSION)){ session_start(); }else{ }
        $this->ses_default = $default;
    }

    public function set_userdata($name)
    {
       return $_SESSION[$this->ses_default][$name];
    }

    public function userdata($name)
    {
        echo $_SESSION[$this->ses_default][$name];
    }

    public function set_flash($type,$msg)
    {
        return $_SESSION["flash"] = [
            "type" => $type, 
            "message" => $msg
        ];

    }

    public function flash($type)
    {
        echo $_SESSION["flash"]["type"].' '.$_SESSION['flash']['message'];
        unset($_SESSION["flash"]);
    }
}