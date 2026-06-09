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
                task_statuses.id AS status_id,
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

    public function findStatuses(): array
    {
        $connection = Database::getConnection();

        $statement = $connection->query(
            'SELECT id, name
             FROM task_statuses
             ORDER BY sort_order ASC'
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $taskId, int $statusId): bool
    {
        $connection = Database::getConnection();

        $statement = $connection->prepare(
            'UPDATE tasks
             SET status_id = :status_id
             WHERE id = :task_id'
        );

        return $statement->execute([
            'status_id' => $statusId,
            'task_id' => $taskId,
        ]);
    }

    public function findById(int $taskId): ?array
    {
        $connection = Database::getConnection();

        $statement = $connection->prepare(
            'SELECT
                tasks.id,
                tasks.project_id,
                tasks.status_id,
                tasks.assigned_user_id,
                tasks.title
             FROM tasks
             WHERE tasks.id = :task_id
             LIMIT 1'
        );

        $statement->execute([
            'task_id' => $taskId,
        ]);

        $task = $statement->fetch(PDO::FETCH_ASSOC);

        return $task ?: null;
    }
}