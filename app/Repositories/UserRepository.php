<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $connection = Database::getConnection();

        $statement = $connection->prepare(
            'SELECT 
                users.id,
                users.email,
                users.password_hash,
                users.first_name,
                users.last_name,
                users.is_active,
                roles.name AS role_name
             FROM users
             JOIN roles ON users.role_id = roles.id
             WHERE users.email = :email
             LIMIT 1'
        );

        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findAll(): array
    {
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

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleActiveStatus(int $userId): bool
    {
        $connection = Database::getConnection();

        $statement = $connection->prepare(
            'UPDATE users
             SET is_active = NOT is_active
             WHERE id = :user_id'
        );

        return $statement->execute([
            'user_id' => $userId,
        ]);
    }
}