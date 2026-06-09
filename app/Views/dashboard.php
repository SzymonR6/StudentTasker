<section class="dashboard">
    <div class="dashboard-header">
        <div>
            <p class="badge">Panel użytkownika</p>
            <h1>Dashboard</h1>
            <p>
                Witaj, <?= htmlspecialchars($user['first_name'] ?? 'Użytkowniku') ?>!
                Jesteś zalogowany jako:
                <strong><?= htmlspecialchars($user['role'] ?? 'brak roli') ?></strong>.
            </p>
        </div>

        <a href="/logout" class="button secondary-button">Wyloguj się</a>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Sesja</h3>
            <p>Status logowania:</p>
            <div class="card-value">Aktywna</div>
        </div>

        <div class="card">
            <h3>Użytkownik</h3>
            <p><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></p>
            <p><?= htmlspecialchars($user['email'] ?? '') ?></p>
        </div>

        <div class="card">
            <h3>Rola</h3>
            <p>Twoja rola w systemie:</p>
            <div class="card-value"><?= htmlspecialchars($user['role'] ?? '') ?></div>
        </div>
    </div>

    <div class="info-box">
        <h2>Etap logowania działa</h2>
        <p>
            Ta strona jest dostępna tylko po zalogowaniu. Jeśli użytkownik nie ma aktywnej sesji,
            zostanie przekierowany na ekran logowania.
        </p>
    </div>
</section>