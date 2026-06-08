<?php

declare(strict_types=1);

session_start();

spl_autoload_register(function (string $className): void {
    $prefix = 'App\\';
    $baseDirectory = __DIR__ . '/../app/';

    if (str_starts_with($className, $prefix)) {
        $relativeClass = substr($className, strlen($prefix));
        $file = $baseDirectory . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});

use App\Controllers\HomeController;
use App\Core\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);