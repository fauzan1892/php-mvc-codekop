<?php
declare(strict_types=1);
namespace System;

use Throwable;

defined('BASEPATH') || exit('No direct script access allowed');

final class Route
{
    private static array $routes = [];
    private static array $guards = [];
    private static array $middlewareStack = [];
    private array $middleware = [];

    private function __construct(private readonly int $index) {}

    public static function get(string $uri, string $action): self
    {
        return self::add('GET', $uri, $action, false);
    }

    public static function head(string $uri, string $action): self
    {
        return self::add('HEAD', $uri, $action, false);
    }

    public static function post(string $uri, string $action): self
    {
        return self::add('POST', $uri, $action, false);
    }

    public static function put(string $uri, string $action): self
    {
        return self::add('PUT', $uri, $action, false);
    }

    public static function patch(string $uri, string $action): self
    {
        return self::add('PATCH', $uri, $action, false);
    }

    public static function delete(string $uri, string $action): self
    {
        return self::add('DELETE', $uri, $action, false);
    }

    public static function options(string $uri, string $action): self
    {
        return self::add('OPTIONS', $uri, $action, false);
    }

    public static function apiGet(string $uri, string $action): self
    {
        return self::add('GET', $uri, $action, true);
    }

    public static function apiHead(string $uri, string $action): self
    {
        return self::add('HEAD', $uri, $action, true);
    }

    public static function apiPost(string $uri, string $action): self
    {
        return self::add('POST', $uri, $action, true);
    }

    public static function apiPut(string $uri, string $action): self
    {
        return self::add('PUT', $uri, $action, true);
    }

    public static function apiPatch(string $uri, string $action): self
    {
        return self::add('PATCH', $uri, $action, true);
    }

    public static function apiDelete(string $uri, string $action): self
    {
        return self::add('DELETE', $uri, $action, true);
    }

    public static function apiOptions(string $uri, string $action): self
    {
        return self::add('OPTIONS', $uri, $action, true);
    }

    public static function resource(string $uri, string $controller): void
    {
        self::get($uri, $controller . '@index');
        self::get($uri . '/create', $controller . '@create');
        self::post($uri, $controller . '@store');
        self::get($uri . '/{id}', $controller . '@show');
        self::get($uri . '/{id}/edit', $controller . '@edit');
        self::put($uri . '/{id}', $controller . '@update');
        self::patch($uri . '/{id}', $controller . '@update');
        self::delete($uri . '/{id}', $controller . '@destroy');
    }

    public static function group(array $options, callable $callback): void
    {
        $middleware = $options['middleware'] ?? [];
        $middleware = is_array($middleware) ? $middleware : [$middleware];
        $previous = self::$middlewareStack;
        self::$middlewareStack = array_values(array_unique(array_merge($previous, $middleware)));

        try {
            $callback();
        } finally {
            self::$middlewareStack = $previous;
        }
    }

    private static function add(string $method, string $uri, string $action, bool $api): self
    {
        self::$routes[] = [
            'method' => $method,
            'uri' => '/' . trim($uri, '/'),
            'action' => $action,
            'name' => null,
            'middleware' => self::$middlewareStack,
            'api' => $api,
        ];
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
            $parameterNames = [];
            $pattern = preg_replace_callback(
                '/\{([A-Za-z_][A-Za-z0-9_]*)\}/',
                static function (array $matches) use (&$parameterNames): string {
                    $parameterNames[] = $matches[1];
                    return '([A-Za-z0-9_-]+)';
                },
                $route['uri']
            );
            if ($pattern === null) continue;

            $matches = [];
            $expression = '#^' . rtrim($pattern, '/') . '/?$#';
            if (preg_match($expression, '/' . trim($path, '/'), $matches) !== 1) continue;

            array_shift($matches);
            $parameters = [];
            foreach ($parameterNames as $index => $name) {
                $parameters[$name] = rawurldecode((string) ($matches[$index] ?? ''));
            }
            return [self::action($route['action']), $route['middleware'], $route['api'] ?? false, $parameters];
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
