<?php 

class Home extends Controller {

    public function index()
    {
        $data['title'] = 'Selamat datang di codekop php mvc';
        $this->show->view('welcome_view', $data);
    }

    public function lihat()
    {
        echo 'sukses';
    }
}