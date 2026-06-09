<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'StudentTasker') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="logo">StudentTasker</div>
        <nav>
            <a href="/">Start</a>
            <a href="/login">Logowanie</a>
            <a href="/dashboard">Dashboard</a>
            <a href="/admin/users">Admin</a>
            <a href="/prototype.html">Prototyp</a>
        </nav>
    </header>

    <main class="container">
        <?php require $viewPath; ?>
    </main>
</body>
</html>