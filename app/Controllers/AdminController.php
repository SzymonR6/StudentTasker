<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Services\AuthService;

class AdminController extends Controller
{
    private AuthService $authService;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userRepository = new UserRepository();
    }

    public function users(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();

        if (!$this->isAdmin($user)) {
            http_response_code(403);

            $this->view('errors/403', [
                'title' => 'Brak dostępu',
                'user' => $user,
            ]);

            return;
        }

        $users = $this->userRepository->findAll();

        $this->view('admin_users', [
            'title' => 'Panel administratora',
            'user' => $user,
            'users' => $users,
        ]);
    }

    public function toggleUserStatus(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();

        if (!$this->isAdmin($user)) {
            http_response_code(403);

            $this->view('errors/403', [
                'title' => 'Brak dostępu',
                'user' => $user,
            ]);

            return;
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            $this->redirect('/admin/users');
        }

        if ($userId === (int) $user['id']) {
            $this->redirect('/admin/users');
        }

        $this->userRepository->toggleActiveStatus($userId);

        $this->redirect('/admin/users');
    }

    private function isAdmin(?array $user): bool
    {
        return ($user['role'] ?? '') === 'admin';
    }
}