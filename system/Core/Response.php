<?php
declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

final class Response
{
    public function __construct(
        private readonly mixed $content = null,
        private readonly int $status = 200,
        private readonly array $headers = [],
    ) {}

    public static function json(mixed $data = null, int $status = 200, array $headers = []): self
    {
        return new self($data, $status, ['Content-Type' => 'application/json; charset=UTF-8'] + $headers);
    }

    public static function noContent(int $status = 204): self
    {
        return new self(null, $status);
    }

    public static function text(string $content, int $status = 200, array $headers = []): self
    {
        return new self($content, $status, ['Content-Type' => 'text/plain; charset=UTF-8'] + $headers);
    }

    public static function redirect(string $location, int $status = 302): self
    {
        if (!in_array($status, [301, 302, 303, 307, 308], true)) {
            throw new \InvalidArgumentException('Invalid redirect status.');
        }
        if (strpbrk($location, "\r\n") !== false) {
            throw new \InvalidArgumentException('Invalid redirect location.');
        }
        return new self(null, $status, ['Location' => $location]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value, true);
        }
        if (in_array($this->status, [204, 304], true)) return;
        $contentType = strtolower((string) ($this->headers['Content-Type'] ?? ''));
        if (str_contains($contentType, 'application/json')) {
            echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            return;
        }
        if ($this->content !== null) echo (string) $this->content;
    }
}
