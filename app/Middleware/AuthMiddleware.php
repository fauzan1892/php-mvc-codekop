<?php
declare(strict_types=1);

namespace App\Middleware;

final class AuthMiddleware implements \System\Middleware
{
    public function handle(): bool
    {
        return isset($_SESSION['user_id']);
    }
}
