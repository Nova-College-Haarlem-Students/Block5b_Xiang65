<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($paginaTitel ?? 'TrapKracht') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <h1>TrapKracht</h1>
        <nav>
            <a href="index.php">Fietsen</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Ingelogd als <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="logout.php">Uitloggen</a>
            <?php else: ?>
                <a href="login.php">Inloggen</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>