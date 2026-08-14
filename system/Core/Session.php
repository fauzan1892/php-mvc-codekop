<?php
declare(strict_types=1);
namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Session Settings
  |--------------------------------------------------------------------------
  |
 */
class Session {
    private string $ses_default;

    public function __construct()
    {
        $this->ses_default = defined('SESSION_NAME') ? SESSION_NAME : 'codekop_session';
    }

    public function session_on(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) return true;
        $sessionPath = defined('SESSION_PATH') ? SESSION_PATH : ROOTPATH . 'storage/sessions';
        if (!is_dir($sessionPath)) {
            mkdir($sessionPath, 0700, true);
        }
        if (is_dir($sessionPath) && is_writable($sessionPath)) {
            session_save_path($sessionPath);
        }
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_name($this->ses_default);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => defined('SESSION_COOKIE_PATH') ? SESSION_COOKIE_PATH : '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => defined('SESSION_SAMESITE') ? SESSION_SAMESITE : 'Lax'
        ]);
        return session_start(['use_strict_mode' => true, 'use_only_cookies' => true]);
    }

    public function regenerate(bool $deleteOldSession = true): bool
    {
        return session_status() === PHP_SESSION_ACTIVE
            && session_regenerate_id($deleteOldSession);
    }

    public function start(): bool
    {
        return $this->session_on();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->session_on();
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    public function set(string|array $key, mixed $value = null): void
    {
        $this->session_on();
        if (is_array($key)) {
            foreach ($key as $name => $item) $_SESSION[(string) $name] = $item;
            return;
        }
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        $this->session_on();
        return array_key_exists($key, $_SESSION);
    }

    public function forget(string|array $key): void
    {
        $this->session_on();
        foreach ((array) $key as $name) unset($_SESSION[(string) $name]);
    }

    public function all(): array
    {
        $this->session_on();
        return $_SESSION;
    }

    public function flush(): void
    {
        $this->session_on();
        $_SESSION = [];
    }

    public function invalidate(): bool
    {
        $this->flush();
        return $this->regenerate(true);
    }

    public function flash(string $key, mixed $value): void
    {
        $this->set('_flash.' . $key, $value);
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $this->get('_flash.' . $key, $default);
        $this->forget('_flash.' . $key);
        return $value;
    }

    public function ses_destroy(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) return true;
        $_SESSION = [];
        $params = session_get_cookie_params();
        if (!headers_sent()) {
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?? '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool) ($params['secure'] ?? false),
                'httponly' => (bool) ($params['httponly'] ?? true),
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        return session_destroy();
    }

    public function set_userdata($name, $value): mixed
    {
       $this->set((string) $name, $value);
       return $value;
    }

    public function userdata($name): mixed
    {
        return $this->get((string) $name);
    }

    public static function set_flashdata($pesan, $aksi, $tipe)
    {
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'aksi'  => $aksi,
            'tipe'  => $tipe
        ];
    }

    public static function flashdata()
    {
        if( isset($_SESSION['flash']) ) {
            $flash = $_SESSION['flash'];
            $type = preg_match('/^[A-Za-z0-9_-]+$/', (string) ($flash['tipe'] ?? 'info'))
                ? (string) $flash['tipe'] : 'info';
            $escape = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            echo '<div class="alert alert-' . $escape($type) . ' alert-dismissible fade show" role="alert">
                    <strong>' . $escape($flash['pesan'] ?? '') . '</strong> ' . $escape($flash['aksi'] ?? '') . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            unset($_SESSION['flash']);
        }
    }
}
