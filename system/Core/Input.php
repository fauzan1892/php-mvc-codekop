<?php

declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

class Input
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function post(?string $name = null, mixed $default = null): mixed
    {
        return $name === null ? $_POST : ($_POST[$name] ?? $default);
    }

    public function get(?string $name = null, mixed $default = null): mixed
    {
        return $name === null ? $_GET : ($_GET[$name] ?? $default);
    }

    public function csrf(): bool
    {
        $token = $this->post('_csrf') ?? $this->post('csrf_token');
        if (!is_string($token) || $token === '') {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        return Security::verifyCsrf(is_string($token) ? $token : null);
    }

    public function getPost(string $name, bool $stripTags = false): mixed
    {
        $value = $_POST[$name] ?? null;
        return $stripTags && is_string($value) ? strip_tags($value) : $value;
    }

    public function getGet(string $name, bool $stripTags = false): mixed
    {
        $value = $_GET[$name] ?? null;
        return $stripTags && is_string($value) ? strip_tags($value) : $value;
    }
}
