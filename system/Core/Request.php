<?php
declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

final class Request
{
    private ?array $json = null;

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    public function url(): string
    {
        return $this->path();
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $_GET : ($_GET[$key] ?? $default);
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        $data = $this->isJson() ? $this->json() : $_POST;
        return $key === null ? $data : ($data[$key] ?? $default);
    }

    public function json(): array
    {
        if ($this->json !== null) return $this->json;
        $raw = file_get_contents('php://input') ?: '';
        if ($raw === '') return $this->json = [];
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            return $this->json = is_array($data) ? $data : [];
        } catch (\JsonException) {
            return $this->json = [];
        }
    }

    public function isJson(): bool
    {
        return str_contains(strtolower($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json');
    }

    public function header(string $name, mixed $default = null): mixed
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if ($name === 'Content-Type') $key = 'CONTENT_TYPE';
        if ($name === 'Content-Length') $key = 'CONTENT_LENGTH';
        return $_SERVER[$key] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $header = (string) $this->header('Authorization', '');
        return preg_match('/^Bearer\s+(.+)$/i', $header, $matches)
            ? trim($matches[1]) : null;
    }

    public function jwtPayload(): ?array
    {
        $payload = $_SERVER['AUTH_JWT_PAYLOAD'] ?? null;
        if (!is_string($payload) || $payload === '') return null;
        $data = json_decode($payload, true);
        return is_array($data) ? $data : null;
    }

    public function wantsJson(): bool
    {
        return $this->isJson()
            || str_starts_with(trim($this->path(), '/'), 'api')
            || str_contains(strtolower((string) $this->header('Accept', '')), 'application/json');
    }
}
