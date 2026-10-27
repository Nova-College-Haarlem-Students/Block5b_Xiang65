<?php
require 'database.php';

// Nieuwe fiets toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toevoegen'])) {
    $sql = "INSERT INTO fiets (merk, type, prijs_per_dag, beschikbaar)
            VALUES (:merk, :type, :prijs, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':merk'  => $_POST['merk'],
        ':type'  => $_POST['type'],
        ':prijs' => $_POST['prijs_per_dag'],
    ]);
    header('Location: index.php');
    exit;
}

// Fiets verwijderen
if (isset($_GET['verwijder'])) {
    $stmt = $pdo->prepare("DELETE FROM fiets WHERE id = :id");
    $stmt->execute([':id' => $_GET['verwijder']]);
    header('Location: index.php');
    exit;
}

// Alle fietsen ophalen
$fietsen = $pdo->query("SELECT * FROM fiets ORDER BY merk")->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Fietsenverhuur</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 2rem auto; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 2rem; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Fietsenverhuur</h1>

    <table>
        <tr>
            <th>ID</th><th>Merk</th><th>Type</th>
            <th>Prijs per dag</th><th>Beschikbaar</th><th></th>
        </tr>
        <?php foreach ($fietsen as $fiets): ?>
            <tr>
                <td><?= $fiets['id'] ?></td>
                <td><?= htmlspecialchars($fiets['merk']) ?></td>
                <td><?= htmlspecialchars($fiets['type']) ?></td>
                <td>€ <?= number_format($fiets['prijs_per_dag'], 2, ',', '.') ?></td>
                <td><?= $fiets['beschikbaar'] ? 'Ja' : 'Nee' ?></td>
                <td>
                    <a href="index.php?verwijder=<?= $fiets['id'] ?>"
                       onclick="return confirm('Weet je het zeker?')">Verwijder</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Fiets toevoegen</h2>
    <form method="post">
        <p><label>Merk: <input type="text" name="merk" required></label></p>
        <p>
            <label>Type:
                <select name="type">
                    <option>Stadsfiets</option>
                    <option>E-bike</option>
                    <option>Tandem</option>
                    <option>Bakfiets</option>
                </select>
            </label>
        </p>
        <p><label>Prijs per dag: <input type="number" step="0.01" name="prijs_per_dag" required></label></p>
        <button type="submit" name="toevoegen">Toevoegen</button>
    </form>
</body>
</html>