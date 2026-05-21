<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : '/';
    }

    public static function query(string $key, ?string $default = null): ?string
    {
        $value = $_GET[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    public static function queryInt(string $key, int $default): int
    {
        $value = self::query($key);

        return $value !== null && ctype_digit($value) ? (int) $value : $default;
    }
}
