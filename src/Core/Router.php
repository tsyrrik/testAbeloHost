<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string, regex:string, handler:callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'regex' => $this->compile($pattern),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $matches) === 1) {
                $params = array_filter(
                    $matches,
                    static fn ($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY,
                );
                ($route['handler'])($params);
                return;
            }
        }

        throw new NotFoundException(sprintf('Route not found: %s %s', $method, $path));
    }

    private function compile(string $pattern): string
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);

        return '#^' . $regex . '$#';
    }
}
