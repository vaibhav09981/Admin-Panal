<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <?php if ($currentUser): ?>
    <header class="topbar">
        <div class="brand">Admin Panel</div>
        <nav class="nav">
            <a href="/dashboard">Dashboard</a>
            <a href="/users">Users</a>
            <a href="/items">Items</a>
        </nav>
        <div class="user">
            <span><?= htmlspecialchars($currentUser['name']) ?> (<?= htmlspecialchars($currentUser['role']) ?>)</span>
            <form method="POST" action="/logout" style="display:inline">
                <button class="btn-small" type="submit">Logout</button>
            </form>
        </div>
    </header>
    <?php endif; ?>
    <main class="container">
        <?php if ($pageError): ?>
            <div class="alert"><?= htmlspecialchars($pageError) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
    <script src="/assets/app.js"></script>
</body>
</html>
