<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../Views/layout.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'Widok nie istnieje: ' . htmlspecialchars($view);
            return;
        }

        require $layoutPath;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}