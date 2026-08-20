<?php

declare(strict_types=1);

namespace App\Middleware;

use System\Input;
use System\Middleware;

final class CsrfMiddleware implements Middleware
{
    public function handle(): bool
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        return (new Input())->csrf();
    }
}
