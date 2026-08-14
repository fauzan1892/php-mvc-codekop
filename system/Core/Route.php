<?php
declare(strict_types=1);
namespace System;

use Throwable;

defined('BASEPATH') || exit('No direct script access allowed');

final class Route
{
    private static array $routes = [];
    private static array $guards = [];
    private array $middleware = [];
    private function __construct(private readonly int $index) {}

    public static function get(string $uri, string $action): self { return self::add('GET', $uri, $action, false); }
    public static function post(string $uri, string $action): self { return self::add('POST', $uri, $action, false); }
    public static function put(string $uri, string $action): self { return self::add('PUT', $uri, $action, false); }
    public static function patch(string $uri, string $action): self { return self::add('PATCH', $uri, $action, false); }
    public static function delete(string $uri, string $action): self { return self::add('DELETE', $uri, $action, false); }
    public static function apiGet(string $uri, string $action): self { return self::add('GET', $uri, $action, true); }
    public static function apiPost(string $uri, string $action): self { return self::add('POST', $uri, $action, true); }
    public static function apiPut(string $uri, string $action): self { return self::add('PUT', $uri, $action, true); }
    public static function apiPatch(string $uri, string $action): self { return self::add('PATCH', $uri, $action, true); }
    public static function apiDelete(string $uri, string $action): self { return self::add('DELETE', $uri, $action, true); }

    private static function add(string $method, string $uri, string $action, bool $api): self
    {
        self::$routes[] = ['method' => $method, 'uri' => $uri, 'action' => $action, 'name' => null, 'middleware' => [], 'api' => $api];
        return new self(array_key_last(self::$routes));
    }

    public static function guard(string $name, callable $callback): void
    {
        self::$guards[$name] = $callback;
    }

    public function middleware(string|array $middleware): self
    {
        $items = is_array($middleware) ? $middleware : [$middleware];
        self::$routes[$this->index]['middleware'] = array_values(array_unique(array_merge(
            self::$routes[$this->index]['middleware'],
            $items
        )));
        return $this;
    }

    public function name(string $name): self
    {
        self::$routes[$this->index]['name'] = $name;
        return $this;
    }

    public static function match(string $method, string $path): ?array
    {
        foreach (self::$routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;
            $pattern = preg_replace('/\{[A-Za-z_][A-Za-z0-9_]*\}/', '[A-Za-z0-9_-]+', $route['uri']);
            if ($pattern !== null && preg_match('#^' . rtrim($pattern, '/') . '/?$#', '/' . trim($path, '/'))) {
                return [self::action($route['action']), $route['middleware'], $route['api'] ?? false];
            }
        }
        return null;
    }

    public static function allows(array $target): bool
    {
        foreach (($target[1] ?? []) as $name) {
            if (isset(self::$guards[$name])) {
                if (!call_user_func(self::$guards[$name])) return false;
                continue;
            }

            if (!self::runMiddleware((string) $name)) return false;
        }
        return true;
    }

    private static function runMiddleware(string $name): bool
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $name)) return false;

        $class = 'App\\Middleware\\' . $name . 'Middleware';
        $file = ROOTPATH . 'app/Middleware/' . $name . 'Middleware.php';
        if (!class_exists($class) && is_file($file)) require_once $file;
        if (!class_exists($class) || !is_subclass_of($class, Middleware::class)) return false;

        try {
            return (new $class())->handle();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            return false;
        }
    }

    private static function action(string $action): array
    {
        $parts = str_contains($action, '@') ? explode('@', $action, 2) : explode('::', $action, 2);
        return [$parts[0] ?? 'Home', $parts[1] ?? 'index'];
    }
}
