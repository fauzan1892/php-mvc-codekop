<?php
declare(strict_types=1);
namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

final class Security
{
    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE && class_exists(Session::class)) {
            (new Session())->session_on();
        }
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }
        return is_string($token) && $token !== '' && isset($_SESSION['_csrf'])
            && hash_equals((string) $_SESSION['_csrf'], $token);
    }

    public static function rotateCsrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            (new Session())->session_on();
        }
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf'];
    }
}
