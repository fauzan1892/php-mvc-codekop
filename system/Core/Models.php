<?php

declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

class Models
{
    protected ?\PDO $db = null;
    protected Views $show;
    protected Session $session;
    protected Input $input;
    protected Crud $crud;

    public function __construct()
    {
        $db = new Database();
        $this->db = $db->connect();
        $this->show = new Views();
        $this->session = new Session();
        $this->input = new Input();
        $this->crud = new Crud();
    }

}
