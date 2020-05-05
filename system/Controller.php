<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
/*
  |--------------------------------------------------------------------------
  | Controller Settings
  |--------------------------------------------------------------------------
  |
 */

class Controller{

    function __construct()
    {
        $db  = new Database;
        $this->db = $db->connect();
        $this->show = new Views;
        $this->session = new Session;
        $this->input = new Input;
        $this->crud = new Crud;
        $this->session->session_on();
    }

    public function model($model)
    {
        require_once 'app/Models/' . $model . '.php';
        return new $model;
    }

}