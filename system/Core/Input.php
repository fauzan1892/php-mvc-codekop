<?php
declare(strict_types=1);
namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Input method Settings ( Get & Post )
  |--------------------------------------------------------------------------
  |
 */

class Input {

    public function method(): string { return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'); }

    public function isPost(): bool { return $this->method() === 'POST'; }

    public function post(?string $name = null, mixed $default = null): mixed {
        return $name === null ? $_POST : ($_POST[$name] ?? $default);
    }

    public function get(?string $name = null, mixed $default = null): mixed {
        return $name === null ? $_GET : ($_GET[$name] ?? $default);
    }

    public function csrf(): bool {
        return Security::verifyCsrf($this->post('_csrf') ?? $this->post('csrf_token'));
    }

    public function getPost($name, $t = null)
    {
        if($t == TRUE)
        {
            return strip_tags($_POST[''.$name.'']);
        }else{
            return $_POST[$name] ?? null;
        }
    }

    public function getGet($name, $t = null)
    {
        if($t == TRUE)
        {
            return strip_tags($_GET[''.$name.'']);
        }else{
            return $_GET[$name] ?? null;
        }
    }

}
