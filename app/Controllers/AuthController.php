<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLoginForm(): void
    {
        $this->view('login', [
            'title' => 'Logowanie',
            'error' => $_SESSION['login_error'] ?? null,
        ]);

        unset($_SESSION['login_error']);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['login_error'] = 'Podaj adres e-mail i hasło.';
            $this->redirect('/login');
        }

        if (!$this->authService->attemptLogin($email, $password)) {
            $_SESSION['login_error'] = 'Nieprawidłowy e-mail, hasło lub konto jest nieaktywne.';
            $this->redirect('/login');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/login');
    }
}