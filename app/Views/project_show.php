<section class="project-details-page">
    <div class="dashboard-header">
        <div>
            <p class="badge">Szczegóły projektu</p>
            <h1><?= htmlspecialchars($project['name']) ?></h1>
            <p><?= htmlspecialchars($project['description'] ?? '') ?></p>
        </div>

        <div class="actions">
            <a href="/tasks/create?project_id=<?= htmlspecialchars((string) $project['id']) ?>" class="button">
                Dodaj zadanie
            </a>

            <a href="/projects" class="button secondary-button">
                Powrót do projektów
            </a>
        </div>
    </div>

    <div class="project-summary">
        <div class="card">
            <h3>Właściciel</h3>
            <p><?= htmlspecialchars($project['owner_name']) ?></p>
        </div>

        <div class="card">
            <h3>Termin</h3>
            <p>
                <?= htmlspecialchars((string) $project['start_date']) ?>
                —
                <?= htmlspecialchars((string) $project['end_date']) ?>
            </p>
        </div>

        <div class="card">
            <h3>Postęp</h3>
            <div class="card-value"><?= htmlspecialchars((string) $project['progress']) ?>%</div>
        </div>
    </div>

    <div class="table-card">
        <h2>Zadania w projekcie</h2>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Zadanie</th>
                    <th>Priorytet</th>
                    <th>Status</th>
                    <th>Przypisano do</th>
                    <th>Termin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $task['id']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($task['title']) ?></strong>
                            <br>
                            <small><?= htmlspecialchars($task['description'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($task['priority']) ?></td>
                        <td>
                            <select
                                class="task-status-select"
                                data-task-id="<?= htmlspecialchars((string) $task['id']) ?>"
                            >
                                <?php foreach ($statuses as $status): ?>
                                    <option
                                        value="<?= htmlspecialchars((string) $status['id']) ?>"
                                        <?= (int) $status['id'] === (int) $task['status_id'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($status['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?= htmlspecialchars($task['assigned_user'] ?? 'Nie przypisano') ?></td>
                        <td><?= htmlspecialchars((string) $task['due_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="/js/tasks.js"></script>