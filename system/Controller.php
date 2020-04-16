<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

class Controller{

    function __construct()
    {
        $this->db   = new Database;
        $this->show = new Views;
        $this->session = new Session;
        $this->input = new Input;
        $this->crud = new Crud($this->db);
    }

    public function model($model)
    {
        require_once 'app/Models/' . $model . '.php';
        return new $model;
    }

}