<section class="task-create-page">
    <div class="dashboard-header">
        <div>
            <p class="badge">Edycja zadania</p>
            <h1>Edytuj zadanie</h1>
            <p>
                Projekt:
                <strong><?= htmlspecialchars($project['name']) ?></strong>
            </p>
        </div>

        <a href="/projects/show?id=<?= htmlspecialchars((string) $project['id']) ?>" class="button secondary-button">
            Powrót do projektu
        </a>
    </div>

    <div class="form-card">
        <?php if (!empty($error)): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/tasks/update" class="app-form">
            <input type="hidden" name="task_id" value="<?= htmlspecialchars((string) $task['id']) ?>">

            <label for="title">Tytuł zadania</label>
            <input
                type="text"
                id="title"
                name="title"
                value="<?= htmlspecialchars($task['title']) ?>"
                required
            >

            <label for="description">Opis zadania</label>
            <textarea
                id="description"
                name="description"
                rows="5"
            ><?= htmlspecialchars($task['description'] ?? '') ?></textarea>

            <label for="status_id">Status</label>
            <select id="status_id" name="status_id" required>
                <?php foreach ($statuses as $status): ?>
                    <option
                        value="<?= htmlspecialchars((string) $status['id']) ?>"
                        <?= (int) $status['id'] === (int) $task['status_id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($status['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="assigned_user_id">Przypisz do użytkownika</label>
            <select id="assigned_user_id" name="assigned_user_id">
                <option value="0">Nie przypisuj</option>
                <?php foreach ($members as $member): ?>
                    <option
                        value="<?= htmlspecialchars((string) $member['id']) ?>"
                        <?= (int) $member['id'] === (int) ($task['assigned_user_id'] ?? 0) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name'] . ' — ' . $member['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="priority">Priorytet</label>
            <select id="priority" name="priority">
                <option value="low" <?= ($task['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Niski</option>
                <option value="normal" <?= ($task['priority'] ?? '') === 'normal' ? 'selected' : '' ?>>Normalny</option>
                <option value="high" <?= ($task['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Wysoki</option>
            </select>

            <label for="due_date">Termin wykonania</label>
            <input
                type="date"
                id="due_date"
                name="due_date"
                value="<?= htmlspecialchars((string) ($task['due_date'] ?? '')) ?>"
            >

            <button type="submit" class="button full-width">
                Zapisz zmiany
            </button>
        </form>
    </div>
</section>