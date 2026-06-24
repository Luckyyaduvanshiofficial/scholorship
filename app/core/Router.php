<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewareStack = [];
    private ?string $currentPrefix = null;

    /**
     * Register a GET route.
     */
    public function get(string $uri, string $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $uri, $handler, $middleware);
    }

    /**
     * Register a POST route.
     */
    public function post(string $uri, string $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $uri, $handler, $middleware);
    }

    /**
     * Group routes with shared middleware and/or prefix.
     */
    public function group(array $options, callable $callback): void
    {
        $previousMiddleware = $this->middlewareStack;
        $previousPrefix     = $this->currentPrefix;

        if (isset($options['middleware'])) {
            $mw = is_array($options['middleware']) ? $options['middleware'] : [$options['middleware']];
            $this->middlewareStack = array_merge($this->middlewareStack, $mw);
        }

        if (isset($options['prefix'])) {
            $this->currentPrefix = ($this->currentPrefix ?? '') . '/' . trim($options['prefix'], '/');
        }

        $callback($this);

        $this->middlewareStack = $previousMiddleware;
        $this->currentPrefix   = $previousPrefix;
    }

    /**
     * Resolve the current request.
     */
    public function resolve(string $method, string $uri): void
    {
        $uri = $this->normalizeUri($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $params = $this->matchUri($route['pattern'], $uri);

            if ($params === false) {
                continue;
            }

            // Execute middleware chain
            $middleware = array_merge($route['groupMiddleware'], $route['routeMiddleware']);

            foreach ($middleware as $mw) {
                if (!$this->runMiddleware($mw)) {
                    // Middleware stopped the request (handled internally)
                    return;
                }
            }

            // Execute controller
            $this->dispatch($route['handler'], $params);

            return;
        }

        Response::abort(404);
    }

    private function addRoute(string $method, string $uri, string $handler, array $middleware): self
    {
        $prefix = $this->currentPrefix ?? '';

        $this->routes[] = [
            'method'          => $method,
            'pattern'         => $prefix . '/' . trim($uri, '/'),
            'handler'         => $handler,
            'routeMiddleware' => $middleware,
            'groupMiddleware' => $this->middlewareStack,
        ];

        return $this;
    }

    private function normalizeUri(string $uri): string
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);

        // Remove trailing slash (keep root)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }

    private function matchUri(string $pattern, string $uri): array|false
    {
        $pattern = rtrim($pattern, '/');
        if ($pattern === '') {
            $pattern = '/';
        }

        // Convert {param} to named regex groups
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            // Filter out numeric keys, keep named params only
            return array_filter($matches, fn ($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    /**
     * Run a middleware. Returns true if the request should continue.
     */
    private function runMiddleware(string $name): bool
    {
        $class = "App\\Middleware\\" . ucfirst($name) . 'Middleware';

        if (!class_exists($class)) {
            Logger::error("Middleware class not found: {$class}");

            return true; // Fail-open for missing middleware; abort would block legit requests
        }

        $instance = new $class();

        $instance->handle();

        return true;
    }

    /**
     * Dispatch to the controller method.
     */
    private function dispatch(string $handler, array $params): void
    {
        [$controller, $method] = explode('@', $handler);

        $class = "App\\Controllers\\{$controller}";

        if (!class_exists($class)) {
            Logger::error("Controller not found: {$class}");
            Response::abort(500, 'Controller not found');
        }

        $instance = new $class();

        if (!method_exists($instance, $method)) {
            Logger::error("Method not found: {$class}@{$method}");
            Response::abort(500, 'Method not found');
        }

        call_user_func_array([$instance, $method], $params);
    }
}
