<section class="hero">
    <div>
        <p class="badge">MVC + PHP + PostgreSQL</p>
        <h1>StudentTasker</h1>
        <p>
            Aplikacja do zarządzania projektami i zadaniami studenckimi.
            Projekt działa już w architekturze MVC i łączy się z bazą danych PostgreSQL.
        </p>

        <div class="actions">
            <a class="button" href="/prototype.html">Zobacz prototyp</a>
        </div>
    </div>

    <div class="status-card">
        <h2>Status aplikacji</h2>
        <p>Połączenie z bazą danych: <strong>aktywne</strong></p>
        <p>Liczba użytkowników testowych w bazie:</p>
        <div class="number"><?= htmlspecialchars((string) $usersCount) ?></div>
    </div>
</section>