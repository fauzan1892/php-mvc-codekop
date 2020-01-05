<?php
    defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

    class welcome{
        
        protected $lihat;
        protected $db;
        function __construct($lihat,$db){
            $this->view = $lihat;
            $this->db = $db;
        }

        public function index()
        {
            $this->view->render_views('welcome');
        }

        public function lihat()
        {
            echo 'lihat disini';
        }
    }
?>