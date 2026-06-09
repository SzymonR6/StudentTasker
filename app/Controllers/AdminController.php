<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\AuthService;

class AdminController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function users(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();

        if (($user['role'] ?? '') !== 'admin') {
            http_response_code(403);

            $this->view('errors/403', [
                'title' => 'Brak dostępu',
                'user' => $user,
            ]);

            return;
        }

        $connection = Database::getConnection();

        $statement = $connection->query(
            'SELECT 
                users.id,
                users.email,
                users.first_name,
                users.last_name,
                users.is_active,
                roles.name AS role_name
             FROM users
             JOIN roles ON users.role_id = roles.id
             ORDER BY users.id ASC'
        );

        $users = $statement->fetchAll();

        $this->view('admin_users', [
            'title' => 'Panel administratora',
            'user' => $user,
            'users' => $users,
        ]);
    }
}