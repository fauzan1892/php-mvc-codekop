<?php
declare(strict_types=1);

$debug_handler = defined('APP_ENV') ? APP_ENV : 'production';
define('APP_DEBUG', in_array($debug_handler, ['testing', 'development'], true));

error_reporting(APP_DEBUG ? E_ALL : 0);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

if (APP_DEBUG) {
    $debugNonce = htmlspecialchars((string) (defined('CSP_NONCE') ? CSP_NONCE : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $debugStyle = '<style nonce="' . $debugNonce . '">
        .codekop-debug-error{margin:1rem;padding:1rem 1.25rem;border:1px solid #f87171;background:#24151a;color:#fecaca;border-radius:8px;font:14px/1.6 ui-monospace,SFMono-Regular,monospace;white-space:pre-wrap;overflow:auto}
        .codekop-debug-error strong{color:#fca5a5}.codekop-debug-error small{color:#fda4af}
    </style>';
    set_error_handler(static function (int $severity, string $message, string $file, int $line) use ($debugStyle): bool {
        if (!(error_reporting() & $severity)) return false;
        $label = match (true) {
            in_array($severity, [E_DEPRECATED, E_USER_DEPRECATED], true) => 'Deprecated',
            in_array($severity, [E_NOTICE, E_USER_NOTICE], true) => 'Notice',
            default => 'PHP error',
        };
        echo $debugStyle . '<div class="codekop-debug-error"><strong>' . $label . '</strong> '
            . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '<br><small>' . htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . ':' . $line . '</small></div>';
        return true;
    });
    set_exception_handler(static function (Throwable $exception) use ($debugStyle): void {
        http_response_code(500);
        echo $debugStyle . '<main class="codekop-debug-error"><strong>Unhandled exception</strong><br>'
            . htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '<br><small>' . htmlspecialchars($exception->getFile(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . ':' . $exception->getLine() . '</small></main>';
    });
} else {
    set_exception_handler(static function (Throwable $exception): void {
        error_log((string) $exception);
        http_response_code(500);
        echo 'Oops! Something went wrong.';
    });
}
