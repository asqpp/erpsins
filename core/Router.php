<?php

namespace Core;

class Router
{
    private $routes = [];
    private $middlewares = [];

    public function get($path, $handler, $middlewares = [])
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post($path, $handler, $middlewares = [])
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put($path, $handler, $middlewares = [])
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete($path, $handler, $middlewares = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute($method, $path, $handler, $middlewares = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path if exists
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $uri = substr($uri, strlen($scriptName));
        }

        $uri = $uri ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                // Run middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareObj = new $middleware();
                    if (!$middlewareObj->handle()) {
                        return;
                    }
                }

                // Execute handler
                if (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $matches);
                } elseif (is_string($route['handler'])) {
                    $this->executeController($route['handler'], $matches);
                }

                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }

    private function convertToRegex($path)
    {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $path . '$#';
    }

    private function executeController($handler, $params)
    {
        [$controller, $method] = explode('@', $handler);
        $controller = "App\\Controllers\\{$controller}";

        if (!class_exists($controller)) {
            throw new \Exception("Controller {$controller} not found");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $method)) {
            throw new \Exception("Method {$method} not found in {$controller}");
        }

        call_user_func_array([$controllerInstance, $method], $params);
    }
}
