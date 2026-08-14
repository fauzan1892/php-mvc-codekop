<?php
declare(strict_types=1);

namespace App\Middleware;

final class AdminMiddleware implements \System\Middleware
{
    public function handle(): bool
    {
        return ($_SESSION['role'] ?? null) === 'admin';
    }
}
