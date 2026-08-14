<?php
declare(strict_types=1);

namespace System;

final class Config
{
    public static string $siteURL = '';
    public static string $timeZone = 'UTC';
    private static array $items = [];

    public static function configure(array $values): void
    {
        self::$items = $values + self::$items;
        self::$siteURL = (string) self::get('base_url', self::$siteURL);
        self::$timeZone = (string) self::get('timezone', self::$timeZone);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) return $default;
            $value = $value[$segment];
        }
        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        self::$items[$key] = $value;
        if ($key === 'base_url') self::$siteURL = (string) $value;
        if ($key === 'timezone') self::$timeZone = (string) $value;
    }

    public static function all(): array
    {
        return self::$items;
    }

    public function config(array $params): void
    {
        self::configure([
            'base_url' => $params['siteURL'] ?? self::$siteURL,
            'timezone' => $params['timeZone'] ?? self::$timeZone,
        ]);
    }

    public static function baseURL(): string
    {
        return self::$siteURL !== '' ? self::$siteURL : (string) self::get('base_url', '');
    }

    public static function timeZone(): bool
    {
        return date_default_timezone_set(self::$timeZone);
    }

    public static function Helper(?array $helpers = null): mixed
    {
        foreach ($helpers ?? [] as $helper) {
            if ($helper === '') return null;
            return include ROOTPATH . 'app/Helper/' . $helper . '.php';
        }
        return null;
    }

    public static function Model(?array $models = null): mixed
    {
        foreach ($models ?? [] as $model) {
            if (!$model) return null;
            $file = ROOTPATH . 'app/Models/' . $model . '.php';
            if (!is_file($file)) return null;
            require_once $file;
            return new $model();
        }
        return null;
    }
}
