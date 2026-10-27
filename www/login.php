<?php
session_start();
require 'database.php';

$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gebruiker zoeken op e-mailadres, samen met de naam van de rol
    $stmt = $pdo->prepare("
        SELECT users.id, users.name, users.password, roles.name AS role
        FROM users
        JOIN roles ON users.role_id = roles.id
        WHERE users.email = :email
    ");
    $stmt->execute([':email' => $_POST['email']]);
    $user = $stmt->fetch();

    // Wachtwoord controleren met de hash uit de database
    if ($user && password_verify($_POST['password'], $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        header('Location: index.php');
        exit;
    }

    // Bewust één melding voor beide gevallen
    $fout = 'E-mailadres of wachtwoord klopt niet.';
}

$paginaTitel = 'Inloggen - TrapKracht';
require 'header.php';
?>

<h2>Inloggen</h2>

<?php if ($fout): ?>
    <p class="fout"><?= htmlspecialchars($fout) ?></p>
<?php endif; ?>

<form method="post">
    <p><label>E-mailadres: <input type="email" name="email" required></label></p>
    <p><label>Wachtwoord: <input type="password" name="password" required></label></p>
    <button type="submit">Inloggen</button>
</form>

<?php require 'footer.php'; ?>