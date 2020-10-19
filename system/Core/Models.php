<?php namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Models Settings
  |--------------------------------------------------------------------------
  |
 */

class Models{

    function __construct()
    {
        $db  = new Database;
        $this->db = $db->connect();
        $this->show = new Views;
        $this->session = new Session;
        $this->input = new Input;
        $this->crud = new Crud;
    }

}