<section class="task-create-page">
    <div class="dashboard-header">
        <div>
            <p class="badge">Nowe zadanie</p>
            <h1>Dodaj zadanie</h1>
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

        <form method="POST" action="/tasks/store" class="app-form">
            <input type="hidden" name="project_id" value="<?= htmlspecialchars((string) $project['id']) ?>">

            <label for="title">Tytuł zadania</label>
            <input
                type="text"
                id="title"
                name="title"
                placeholder="Np. Przygotować dokumentację"
                required
            >

            <label for="description">Opis zadania</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="Krótki opis zadania"
            ></textarea>

            <label for="status_id">Status</label>
            <select id="status_id" name="status_id" required>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars((string) $status['id']) ?>">
                        <?= htmlspecialchars($status['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="assigned_user_id">Przypisz do użytkownika</label>
            <select id="assigned_user_id" name="assigned_user_id">
                <option value="0">Nie przypisuj</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?= htmlspecialchars((string) $member['id']) ?>">
                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name'] . ' — ' . $member['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="priority">Priorytet</label>
            <select id="priority" name="priority">
                <option value="low">Niski</option>
                <option value="normal" selected>Normalny</option>
                <option value="high">Wysoki</option>
            </select>

            <label for="due_date">Termin wykonania</label>
            <input type="date" id="due_date" name="due_date">

            <button type="submit" class="button full-width">
                Dodaj zadanie
            </button>
        </form>
    </div>
</section>