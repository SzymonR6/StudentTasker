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

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\TaskApiController;
use App\Core\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/admin/users', [AdminController::class, 'users']);

$router->get('/projects', [ProjectController::class, 'index']);
$router->get('/projects/show', [ProjectController::class, 'show']);

$router->post('/api/tasks/status', [TaskApiController::class, 'updateStatus']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);