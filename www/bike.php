<?php
session_start();
require 'database.php';

// Het id uit de link (bike.php?id=3) als getal ophalen
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT bikes.*, bike_types.name AS type_name
    FROM bikes
    JOIN bike_types ON bikes.bike_type_id = bike_types.id
    WHERE bikes.id = :id AND bikes.deleted_at IS NULL
");
$stmt->execute([':id' => $id]);
$bike = $stmt->fetch();

// Bestaat de fiets niet (of is hij verwijderd)? Dan een nette melding
if (!$bike) {
    http_response_code(404);
    $paginaTitel = 'Fiets niet gevonden - TrapKracht';
    require 'header.php';
    echo '<h2>Fiets niet gevonden</h2>';
    echo '<p><a href="index.php">&larr; Terug naar alle fietsen</a></p>';
    require 'footer.php';
    exit;
}

$fotoBestand = basename($bike['photo']);
$fotoBestaat = file_exists(__DIR__ . '/images/' . $fotoBestand);

$paginaTitel = $bike['brand'] . ' - TrapKracht';
require 'header.php';
?>

<p><a href="index.php"> Terug naar alle fietsen</a></p>

<div class="bike-detail">
    <?php if ($fotoBestaat): ?>
        <img src="images/<?= htmlspecialchars($fotoBestand) ?>"
             alt="<?= htmlspecialchars($bike['brand'] . ' ' . $bike['type_name']) ?>">
    <?php else: ?>
        <div class="bike-noimg">Geen foto</div>
    <?php endif; ?>

    <div>
        <h2><?= htmlspecialchars($bike['brand']) ?></h2>
        <table>
            <tr><th>Type</th><td><?= htmlspecialchars($bike['type_name']) ?></td></tr>
            <tr><th>Framemaat</th><td><?= $bike['frame_size'] ?> cm</td></tr>
            <tr><th>Prijs per dag</th><td>€ <?= number_format($bike['price_per_day'], 2, ',', '.') ?></td></tr>
        </table>
    </div>
</div>

<?php require 'footer.php'; ?>