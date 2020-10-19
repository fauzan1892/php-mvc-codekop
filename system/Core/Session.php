<?php namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Session Settings
  |--------------------------------------------------------------------------
  |
 */
use App\System;

class Session {

    public function __construct()
    {
        $default = 'codekop_session';
        $this->ses_default = $default;
    }

    public function session_on()
    {
        return session_start();
    }

    public function ses_destroy()
    {
        return session_destroy();
    }

    public function set_userdata($name,$value)
    {
       return $_SESSION[$name] = $value;
    }

    public function userdata($name)
    {
        return $_SESSION[$name];
    }

    public static function set_flashdata($pesan, $aksi, $tipe)
    {
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'aksi'  => $aksi,
            'tipe'  => $tipe
        ];
    }

    public static function flashdata()
    {
        if( isset($_SESSION['flash']) ) {
            echo '<div class="alert alert-' . $_SESSION['flash']['tipe'] . ' alert-dismissible fade show" role="alert">
                    <strong>' . $_SESSION['flash']['pesan'] . '</strong> ' . $_SESSION['flash']['aksi'] . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            unset($_SESSION['flash']);
        }
    }
}