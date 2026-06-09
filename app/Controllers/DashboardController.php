<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class DashboardController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();

        $this->view('dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
        ]);
    }
}