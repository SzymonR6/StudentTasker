<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class TaskRepository
{
    public function findByProjectId(int $projectId): array
    {
        $connection = Database::getConnection();

        $statement = $connection->prepare(
            'SELECT
                tasks.id,
                tasks.title,
                tasks.description,
                tasks.priority,
                tasks.due_date,
                task_statuses.name AS status_name,
                users.first_name || \' \' || users.last_name AS assigned_user
             FROM tasks
             JOIN task_statuses ON tasks.status_id = task_statuses.id
             LEFT JOIN users ON tasks.assigned_user_id = users.id
             WHERE tasks.project_id = :project_id
             ORDER BY task_statuses.sort_order ASC, tasks.id ASC'
        );

        $statement->execute([
            'project_id' => $projectId,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}