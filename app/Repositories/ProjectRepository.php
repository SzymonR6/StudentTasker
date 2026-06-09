<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProjectRepository
{
    public function findAllForUser(int $userId, string $role): array
    {
        $connection = Database::getConnection();

        if ($role === 'admin') {
            $statement = $connection->query(
                'SELECT 
                    projects.id,
                    projects.name,
                    projects.description,
                    projects.start_date,
                    projects.end_date,
                    users.first_name || \' \' || users.last_name AS owner_name,
                    get_project_progress(projects.id) AS progress
                 FROM projects
                 JOIN users ON projects.owner_id = users.id
                 ORDER BY projects.id ASC'
            );

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        $statement = $connection->prepare(
            'SELECT 
                projects.id,
                projects.name,
                projects.description,
                projects.start_date,
                projects.end_date,
                users.first_name || \' \' || users.last_name AS owner_name,
                get_project_progress(projects.id) AS progress
             FROM projects
             JOIN users ON projects.owner_id = users.id
             JOIN project_members ON projects.id = project_members.project_id
             WHERE project_members.user_id = :user_id
             ORDER BY projects.id ASC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByIdForUser(int $projectId, int $userId, string $role): ?array
    {
        $connection = Database::getConnection();

        if ($role === 'admin') {
            $statement = $connection->prepare(
                'SELECT 
                    projects.id,
                    projects.name,
                    projects.description,
                    projects.start_date,
                    projects.end_date,
                    users.first_name || \' \' || users.last_name AS owner_name,
                    get_project_progress(projects.id) AS progress
                 FROM projects
                 JOIN users ON projects.owner_id = users.id
                 WHERE projects.id = :project_id
                 LIMIT 1'
            );

            $statement->execute([
                'project_id' => $projectId,
            ]);

            $project = $statement->fetch(PDO::FETCH_ASSOC);

            return $project ?: null;
        }

        $statement = $connection->prepare(
            'SELECT 
                projects.id,
                projects.name,
                projects.description,
                projects.start_date,
                projects.end_date,
                users.first_name || \' \' || users.last_name AS owner_name,
                get_project_progress(projects.id) AS progress
             FROM projects
             JOIN users ON projects.owner_id = users.id
             JOIN project_members ON projects.id = project_members.project_id
             WHERE projects.id = :project_id
               AND project_members.user_id = :user_id
             LIMIT 1'
        );

        $statement->execute([
            'project_id' => $projectId,
            'user_id' => $userId,
        ]);

        $project = $statement->fetch(PDO::FETCH_ASSOC);

        return $project ?: null;
    }
}