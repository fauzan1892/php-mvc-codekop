<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

use \System\Controller;

class Home extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->model('Test');
    }
    public function index()
    {
        $data['title'] = 'Selamat datang di codekop php mvc';
        $this->show->view('welcome_view', $data);
    }

    public function test()
    {
        $data = [
            'title'         => 'Program testing',
            'page_title'    => 'Page Testing'
        ];

        $this->show->view('tes_view', $data);
    }
}