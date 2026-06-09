<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
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

    public function createTaskForm(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();
        $projectId = (int) ($_GET['project_id'] ?? 0);

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

        $statuses = $this->taskRepository->findStatuses();
        $members = $this->findProjectMembers($projectId);

        $this->view('task_create', [
            'title' => 'Dodaj zadanie',
            'user' => $user,
            'project' => $project,
            'statuses' => $statuses,
            'members' => $members,
            'error' => $_SESSION['task_error'] ?? null,
        ]);

        unset($_SESSION['task_error']);
    }

    public function storeTask(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $this->authService->user();

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $statusId = (int) ($_POST['status_id'] ?? 0);
        $assignedUserId = (int) ($_POST['assigned_user_id'] ?? 0);
        $priority = $_POST['priority'] ?? 'normal';
        $dueDate = $_POST['due_date'] ?? null;

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

        if ($title === '' || $statusId <= 0) {
            $_SESSION['task_error'] = 'Podaj tytuł zadania i wybierz status.';
            $this->redirect('/tasks/create?project_id=' . $projectId);
        }

        $this->taskRepository->create([
            'project_id' => $projectId,
            'status_id' => $statusId,
            'assigned_user_id' => $assignedUserId,
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'due_date' => $dueDate,
        ]);

        $this->redirect('/projects/show?id=' . $projectId);
    }

    private function findProjectMembers(int $projectId): array
    {
        $connection = Database::getConnection();

        $statement = $connection->prepare(
            'SELECT 
                users.id,
                users.first_name,
                users.last_name,
                users.email
             FROM project_members
             JOIN users ON project_members.user_id = users.id
             WHERE project_members.project_id = :project_id
               AND users.is_active = TRUE
             ORDER BY users.first_name ASC, users.last_name ASC'
        );

        $statement->execute([
            'project_id' => $projectId,
        ]);

        return $statement->fetchAll();
    }
}