<?php

declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

class Controller
{
    protected ?\PDO $db = null;
    protected Views $show;
    protected Session $session;
    protected Input $input;
    protected Crud $crud;
    protected Security $security;
    protected Views $load;
    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $db = new Database();
        $this->db = $db->connect();
        $this->show = new Views();
        $this->load = $this->show;
        $this->session = new Session();
        $this->input = new Input();
        $this->crud = new Crud();
        $this->session->session_on();
        $this->security = new Security();
        $this->request = new Request();
        $this->response = new Response();
    }

    public function model(string $model): object
    {
        $class = str_contains($model, '\\') ? $model : 'App\\Models\\' . $model;
        $relative = str_replace('App\\Models\\', '', $class);
        $shortName = basename(str_replace('\\', '/', $class));
        $file = ROOTPATH . 'app/Models/' . str_replace('\\', '/', $relative) . '.php';
        if (!is_file($file)) {
            $file = ROOTPATH . 'app/Models/' . $shortName . '.php';
        }
        if (!is_file($file)) {
            throw new \RuntimeException('Model not found: ' . $model);
        }
        require_once $file;

        $resolvedClass = class_exists($class) ? $class : $shortName;
        if (!class_exists($resolvedClass)) {
            throw new \RuntimeException('Model class not found: ' . $model);
        }
        return new $resolvedClass();
    }

}
