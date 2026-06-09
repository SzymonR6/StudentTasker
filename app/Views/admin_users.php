<section class="admin-page">
    <div class="dashboard-header">
        <div>
            <p class="badge">Panel administratora</p>
            <h1>Zarządzanie użytkownikami</h1>
            <p>
                Witaj, <?= htmlspecialchars($user['first_name'] ?? 'Admin') ?>.
                Ta strona jest dostępna tylko dla roli <strong>admin</strong>.
            </p>
        </div>

        <a href="/dashboard" class="button secondary-button">Powrót do dashboardu</a>
    </div>

    <div class="table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię i nazwisko</th>
                    <th>E-mail</th>
                    <th>Rola</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $account): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $account['id']) ?></td>
                        <td>
                            <?= htmlspecialchars($account['first_name'] . ' ' . $account['last_name']) ?>
                        </td>
                        <td><?= htmlspecialchars($account['email']) ?></td>
                        <td>
                            <span class="role-badge">
                                <?= htmlspecialchars($account['role_name']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($account['is_active']): ?>
                                <span class="status active">Aktywny</span>
                            <?php else: ?>
                                <span class="status inactive">Nieaktywny</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int) $account['id'] === (int) $user['id']): ?>
                                <span class="muted-text">To jest Twoje konto</span>
                            <?php else: ?>
                                <form
                                    method="POST"
                                    action="/admin/users/toggle-status"
                                    onsubmit="return confirm('Czy na pewno chcesz zmienić status tego użytkownika?');"
                                >
                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?= htmlspecialchars((string) $account['id']) ?>"
                                    >

                                    <?php if ($account['is_active']): ?>
                                        <button type="submit" class="danger-button">
                                            Dezaktywuj
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="success-button">
                                            Aktywuj
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>