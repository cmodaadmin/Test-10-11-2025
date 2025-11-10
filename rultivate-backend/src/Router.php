<?php
namespace Rultivate;

use Rultivate\Utils\Response;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function use($middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function add(string $method, string $path, callable $handler, array $options = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/api' . $path,
            'handler' => $handler,
            'options' => $options
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            $pattern = '#^' . preg_replace('#\{([^/]+)\}#', '(?P<$1>[^/]+)', $route['path']) . '$#';
            if ($route['method'] === strtoupper($method) && preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY);
                $request = [
                    'method' => $method,
                    'path' => $path,
                    'params' => $params,
                    'body' => json_decode(file_get_contents('php://input'), true) ?? [],
                    'query' => $_GET,
                    'headers' => getallheaders() ?: []
                ];

                $handler = $route['handler'];
                $middlewareStack = $this->buildMiddlewareStack($handler, $route['options']);
                return $middlewareStack($request);
            }
        }
        Response::json(['message' => 'Not Found'], 404);
    }

    private function buildMiddlewareStack(callable $handler, array $options): callable
    {
        $stack = array_reverse($this->middleware);
        $next = function ($request) use ($handler) {
            return $handler($request);
        };

        foreach ($stack as $middleware) {
            $next = fn($request) => $middleware->handle($request, $next, $options);
        }
        return $next;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
