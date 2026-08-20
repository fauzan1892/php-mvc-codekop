<?php
declare(strict_types=1);
/*
  |--------------------------------------------------------------------------
  | Codekop PHP MVC Frameworks
  |--------------------------------------------------------------------------
  | is a simple open web project using php mvc frameworks 
  | simple php mvc made for the purpose of learning and new things about php 
  | with php mvc concept.
  | 
  | @package   : php-mvc-codekop
  | @author    : Codekop Dev Team
  | @copyright : Copyright (c) 2019-2026 Codekop.com (https://www.codekop.com)
  |
  | free for everyone to development a simple open web project using php mvc 
  | recommended php version for running for php 8.2+
  | 
 */

$minimumPhp = '8.2.0';
if (PHP_VERSION_ID < 80200) {
    http_response_code(500);
    exit('PHP 8.2 atau lebih baru diperlukan. Versi aktif: ' . PHP_VERSION);
}

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', ROOTPATH);
define('CSP_NONCE', base64_encode(random_bytes(24)));

// Load the small config early so CSP can distinguish development tooling.
require_once ROOTPATH . 'system/Config/Config.php';
require_once ROOTPATH . 'app/Config/Config.php';

// Baseline OWASP security headers. A controller may add a nonce if it needs inline JS.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    $developmentStyleAttributes = (defined('APP_ENV') && APP_ENV === 'development')
        ? "; style-src-attr 'unsafe-inline'"
        : '';
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'; object-src 'none'; img-src 'self' data:; style-src 'self' 'nonce-" . CSP_NONCE . "'" . $developmentStyleAttributes . "; script-src 'self' 'nonce-" . CSP_NONCE . "'; connect-src 'self'");
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

require_once ROOTPATH . 'system/run.php';
