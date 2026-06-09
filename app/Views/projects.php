<section class="projects-page">
    <div class="dashboard-header">
        <div>
            <p class="badge">Projekty</p>
            <h1>Moje projekty</h1>
            <p>
                Lista projektów dostępnych dla zalogowanego użytkownika:
                <strong><?= htmlspecialchars($user['first_name'] ?? '') ?></strong>.
            </p>
        </div>

        <a href="/dashboard" class="button secondary-button">Powrót do dashboardu</a>
    </div>

    <div class="project-grid">
        <?php foreach ($projects as $project): ?>
            <article class="project-card">
                <div>
                    <h2><?= htmlspecialchars($project['name']) ?></h2>
                    <p><?= htmlspecialchars($project['description'] ?? '') ?></p>
                </div>

                <div class="project-meta">
                    <p><strong>Właściciel:</strong> <?= htmlspecialchars($project['owner_name']) ?></p>
                    <p><strong>Start:</strong> <?= htmlspecialchars((string) $project['start_date']) ?></p>
                    <p><strong>Koniec:</strong> <?= htmlspecialchars((string) $project['end_date']) ?></p>
                </div>

                <div class="progress-wrapper">
                    <div class="progress-label">
                        <span>Postęp projektu</span>
                        <strong><?= htmlspecialchars((string) $project['progress']) ?>%</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= htmlspecialchars((string) $project['progress']) ?>%;"></div>
                    </div>
                </div>

                <a class="button" href="/projects/show?id=<?= htmlspecialchars((string) $project['id']) ?>">
                    Zobacz szczegóły
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>