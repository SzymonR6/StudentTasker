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
            $this->showErrorPage(404, 'errors/404', 'Nie znaleziono strony');
            return;
        }

        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            $this->showErrorPage(500, 'errors/404', 'Błąd aplikacji');
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            $this->showErrorPage(500, 'errors/404', 'Błąd aplikacji');
            return;
        }

        $controller->$method();
    }

    private function showErrorPage(int $statusCode, string $view, string $title): void
    {
        http_response_code($statusCode);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../Views/layout.php';

        if (file_exists($viewPath) && file_exists($layoutPath)) {
            require $layoutPath;
            return;
        }

        echo $title;
    }
}