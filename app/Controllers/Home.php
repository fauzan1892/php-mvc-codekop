<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

use \System\Controller;

class Home extends Controller {

    public function index()
    {
        $data['title'] = 'Selamat datang di codekop php mvc';
        $this->show->view('welcome_view', $data);
    }

}