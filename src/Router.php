<?php

declare(strict_types=1);

namespace App;

use App\Http\Request;

/**
 * Minimal front-controller router supporting GET, POST and named route parameters.
 */
final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array{0: string, 1: string}}> */
    private array $routes = [];

    public function __construct(private readonly Request $request) {}

    /**
     * Register a GET route.
     *
     * @param  string                    $pattern  URL pattern (e.g. '/products/{id}').
     * @param  array{0: string, 1: string}  $handler  [ControllerFQN, methodName].
     *
     * @return void
     */
    public function get(string $pattern, array $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    /**
     * Register a POST route.
     *
     * @param  string                    $pattern  URL pattern.
     * @param  array{0: string, 1: string}  $handler  [ControllerFQN, methodName].
     *
     * @return void
     */
    public function post(string $pattern, array $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    /**
     * Match the current request and invoke the controller method.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $requestMethod = $this->request->getMethod();
        $requestPath   = $this->request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $routeParams = $this->matchPattern($route['pattern'], $requestPath);

            if ($routeParams === null) {
                continue;
            }

            [$controllerClass, $actionMethod] = $route['handler'];
            $controllerInstance = new $controllerClass($this->request);
            $controllerInstance->$actionMethod(...array_values($routeParams));

            return;
        }

        http_response_code(404);
        echo '<h1>404 — Page Not Found</h1>';
    }

    /**
     * Store a route definition.
     *
     * @param  string                       $method   HTTP method.
     * @param  string                       $pattern  URL pattern.
     * @param  array{0: string, 1: string}  $handler  Controller + method pair.
     *
     * @return void
     */
    private function addRoute(string $method, string $pattern, array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * Attempt to match a URL pattern against a request path.
     * Returns extracted named parameters on match, null on mismatch.
     *
     * @param  string  $pattern      Route pattern (e.g. '/products/{id}').
     * @param  string  $requestPath  Actual URL path (e.g. '/products/42').
     *
     * @return array<string, string>|null  Named capture groups, or null if no match.
     */
    private function matchPattern(string $pattern, string $requestPath): ?array
    {
        $regexPattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regexPattern = '#^' . $regexPattern . '$#';

        if (!preg_match($regexPattern, $requestPath, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
