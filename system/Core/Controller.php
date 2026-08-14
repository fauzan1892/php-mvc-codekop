<?php
declare(strict_types=1);
namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Controller Settings
  |--------------------------------------------------------------------------
  |
 */

class Controller{
    protected ?\PDO $db = null;
    protected Views $show;
    protected Session $session;
    protected Input $input;
    protected Crud $crud;
    protected Security $security;
    protected Views $load;
    protected Request $request;
    protected Response $response;

    function __construct()
    {
        $db  = new Database;
        $this->db = $db->connect();
        $this->show = new Views;
        $this->load = $this->show;
        $this->session = new Session;
        $this->input = new Input;
        $this->crud = new Crud;
        $this->session->session_on();
        $this->security = new Security;
        $this->request = new Request;
        $this->response = new Response;
    }

    public function model($model)
    {
        require_once 'app/Models/' . $model . '.php';
        return new $model;
    }

}
