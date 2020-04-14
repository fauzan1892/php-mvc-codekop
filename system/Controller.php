<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

class Controller{

    function __construct()
    {
        $this->db   = new Database;
        $this->show = new Views;
        $this->crud = new Crud($this->db);
    }


}