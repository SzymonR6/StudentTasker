<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $connection = Database::getConnection();

        $statement = $connection->query('SELECT COUNT(*) AS users_count FROM users');
        $result = $statement->fetch();

        $this->view('home', [
            'title' => 'Strona główna',
            'usersCount' => $result['users_count'] ?? 0,
        ]);
    }
}