<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Services\AuthService;

class ProjectController extends Controller
{
    private AuthService $authService;
    private ProjectRepository $projectRepository;
    private TaskRepository $taskRepository;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->projectRepository = new ProjectRepository();
        $this->taskRepository = new TaskRepository();
    }

    public function index(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();

        $projects = $this->projectRepository->findAllForUser(
            (int) $user['id'],
            (string) $user['role']
        );

        $this->view('projects', [
            'title' => 'Projekty',
            'user' => $user,
            'projects' => $projects,
        ]);
    }

    public function show(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();
        $projectId = (int) ($_GET['id'] ?? 0);

        if ($projectId <= 0) {
            http_response_code(404);
            echo 'Nie znaleziono projektu.';
            return;
        }

        $project = $this->projectRepository->findByIdForUser(
            $projectId,
            (int) $user['id'],
            (string) $user['role']
        );

        if ($project === null) {
            http_response_code(403);
            $this->view('errors/403', [
                'title' => 'Brak dostępu',
                'user' => $user,
            ]);
            return;
        }

        $tasks = $this->taskRepository->findByProjectId($projectId);
        $statuses = $this->taskRepository->findStatuses();

            $this->view('project_show', [
            'title' => $project['name'],
            'user' => $user,
            'project' => $project,
            'tasks' => $tasks,
            'statuses' => $statuses,
        ]);
    }
}