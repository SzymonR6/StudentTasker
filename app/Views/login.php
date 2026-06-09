<section class="auth-page">
    <div class="auth-card">
        <p class="badge">StudentTasker</p>
        <h1>Logowanie</h1>
        <p class="auth-description">
            Zaloguj się, aby przejść do panelu zarządzania projektami i zadaniami.
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="auth-form">
            <label for="email">Adres e-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="admin@studenttasker.local"
                required
            >

            <label for="password">Hasło</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="password"
                required
            >

            <button type="submit" class="button full-width">
                Zaloguj się
            </button>
        </form>

        <div class="test-accounts">
            <h3>Dane testowe</h3>
            <p><strong>Admin:</strong> admin@studenttasker.local</p>
            <p><strong>Lider:</strong> anna@studenttasker.local</p>
            <p><strong>Student:</strong> jan@studenttasker.local</p>
            <p><strong>Hasło:</strong> password</p>
        </div>
    </div>
</section>