<?php
declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

final class RouteGroup
{
    private array $middleware = [];

    public function __construct(private readonly string $prefix)
    {
    }

    public function middleware(string|array $middleware): self
    {
        $this->middleware = is_array($middleware) ? $middleware : [$middleware];
        return $this;
    }

    public function group(callable $callback): void
    {
        Route::group([
            'prefix' => $this->prefix,
            'middleware' => $this->middleware,
        ], $callback);
    }
}
