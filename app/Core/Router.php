<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        $handler = $this->routes[$requestMethod][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Nie znaleziono strony';
            return;
        }

        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo 'Kontroler nie istnieje';
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo 'Metoda kontrolera nie istnieje';
            return;
        }

        $controller->$method();
    }
}