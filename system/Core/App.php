<?php
declare(strict_types=1);
namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

final class App
{
    private Request $request;

    public function __construct(string $controller, bool $active = false)
    {
        $this->request = new Request();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            (new Session())->session_on();
        }
        $route = $this->route();
        $target = $active ? $this->configuredTarget($route) : null;
        if ($target !== null) {
            if (!Route::allows($target)) {
                $this->forbidden((bool) ($target[2] ?? false));
                return;
            }
            $this->dispatchTarget($target[0][0], $target[0][1], (bool) ($target[2] ?? false));
            return;
        }
        if (!$active) {
            $this->notFound($this->request->wantsJson() || str_starts_with($route, 'api'));
            return;
        }
        $this->dispatch($route, $controller);
    }

    private function route(): string
    {
        $url = filter_input(INPUT_GET, 'url', FILTER_UNSAFE_RAW);
        if (!is_string($url) || $url === '') {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $base = parse_url((string) base_url, PHP_URL_PATH) ?: '/';
            $url = str_starts_with($path, rtrim($base, '/'))
                ? substr($path, strlen(rtrim($base, '/'))) : $path;
        }
        $url = trim(rawurldecode($url), '/');
        $parts = array_values(array_filter(explode('/', $url), static fn(string $part): bool => $part !== ''));
        return implode('/', array_map(
            static fn(string $part): string => preg_replace('/[^A-Za-z0-9_.-]/', '', $part),
            $parts
        ));
    }

    private function configuredTarget(string $route): ?array
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = '/' . $route;
        $modernTarget = Route::match($method, $path);
        if ($modernTarget !== null) return $modernTarget;
        foreach (($GLOBALS['routes']['routes'] ?? []) as $definition => $target) {
            [$verb, $pattern] = array_pad(explode(' ', trim((string) $definition), 2), 2, '');
            if (strtoupper($verb) !== $method) continue;
            $regex = preg_replace('/\{[A-Za-z_][A-Za-z0-9_]*\}/', '[A-Za-z0-9_-]+', $pattern);
            if ($regex !== null && preg_match('#^' . rtrim($regex, '/') . '/?$#', $path)) {
                return $this->target((string) $target);
            }
        }
        return null;
    }

    private function dispatch(string $route, string $default): void
    {
        if ($route === '') {
            [$controller, $method] = $this->target($default);
        } else {
            $segments = explode('/', $route);
            $controller = ucfirst($segments[0] ?? '');
            $method = $segments[1] ?? 'index';
        }
        $this->dispatchTarget($controller, $method, $this->request->wantsJson() || str_starts_with($route, 'api'));
    }

    private function target(string $target): array
    {
        $parts = explode('::', $target, 2);
        return [$parts[0] ?? 'Home', $parts[1] ?? 'index'];
    }

    private function dispatchTarget(string $controller, string $method, bool $api = false): void
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*(?:\/[A-Za-z][A-Za-z0-9_]*)*$/', $controller)
            || !preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $method)) {
            $this->notFound($api); return;
        }
        $file = ROOTPATH . 'app/Controllers/' . $controller . '.php';
        if (!is_file($file)) { $this->notFound($api); return; }
        require_once $file;
        $class = basename(str_replace('\\', '/', $controller));
        $namespacedClass = str_contains($controller, '/')
            ? 'App\\Controllers\\' . str_replace('/', '\\', $controller)
            : $class;
        $resolvedClass = class_exists($namespacedClass) ? $namespacedClass : $class;
        if (!class_exists($resolvedClass) || !is_subclass_of($resolvedClass, Controller::class)) {
            $this->notFound($api); return;
        }
        try {
            $reflection = new \ReflectionMethod($resolvedClass, $method);
        } catch (\ReflectionException) {
            $this->notFound($api); return;
        }
        if (!$reflection->isPublic() || $reflection->isStatic() || str_starts_with($method, '__')) {
            $this->notFound($api); return;
        }
        $object = new $resolvedClass();
        $result = $object->$method();
        if ($result instanceof Response) {
            $result->send();
        } elseif ($api && is_array($result)) {
            Response::json(['data' => $result])->send();
        } elseif ($api && $result !== null) {
            Response::json(['data' => $result])->send();
        }
    }

    private function forbidden(bool $api): void
    {
        if ($api) {
            Response::json(['error' => ['code' => 'forbidden', 'message' => 'Forbidden']], 403)->send();
            return;
        }
        http_response_code(403);
        echo 'Forbidden';
    }

    private function notFound(bool $api = false): void
    {
        if ($api || $this->request->wantsJson()) {
            Response::json(['error' => ['code' => 'not_found', 'message' => 'Resource not found']], 404)->send();
            return;
        }
        http_response_code(404);
        $view = ROOTPATH . 'app/Views/errors/error_404.php';
        if (is_file($view)) require $view;
    }
}
