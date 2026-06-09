<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Services\AuthService;

class TaskApiController
{
    private AuthService $authService;
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->taskRepository = new TaskRepository();
        $this->projectRepository = new ProjectRepository();
    }

    public function updateStatus(): void
    {
        header('Content-Type: application/json');

        if (!$this->authService->isLoggedIn()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Użytkownik nie jest zalogowany.',
            ]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $taskId = (int) ($input['task_id'] ?? 0);
        $statusId = (int) ($input['status_id'] ?? 0);

        if ($taskId <= 0 || $statusId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Nieprawidłowe dane zadania lub statusu.',
            ]);
            return;
        }

        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Nie znaleziono zadania.',
            ]);
            return;
        }

        $user = $this->authService->user();

        $project = $this->projectRepository->findByIdForUser(
            (int) $task['project_id'],
            (int) $user['id'],
            (string) $user['role']
        );

        if ($project === null) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Brak dostępu do tego zadania.',
            ]);
            return;
        }

        $updated = $this->taskRepository->updateStatus($taskId, $statusId);

        echo json_encode([
            'success' => $updated,
            'message' => $updated ? 'Status zadania został zmieniony.' : 'Nie udało się zmienić statusu.',
        ]);
    }
}