<?php
session_start();
require 'database.php';

// Is de bezoeker ingelogd als admin?
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$fout = '';

// Fiets toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toevoegen'])) {
    if (!$isAdmin) {
        header('Location: login.php');
        exit;
    }

    $foto = $_FILES['photo'] ?? null;
    $toegestaan = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
        $fout = 'Kies een foto om te uploaden.';
    } elseif (!isset($toegestaan[mime_content_type($foto['tmp_name'])])) {
        $fout = 'Alleen JPG, PNG of WEBP is toegestaan.';
    } elseif ($foto['size'] > 2 * 1024 * 1024) {
        $fout = 'De foto mag maximaal 2 MB zijn.';
    } else {
        // Unieke bestandsnaam, zodat bestanden elkaar niet overschrijven
        $extensie = $toegestaan[mime_content_type($foto['tmp_name'])];
        $naam = bin2hex(random_bytes(8)) . '.' . $extensie;

        if (!is_dir(__DIR__ . '/images')) {
            mkdir(__DIR__ . '/images', 0755, true);
        }

        if (move_uploaded_file($foto['tmp_name'], __DIR__ . '/images/' . $naam)) {
            $stmt = $pdo->prepare("
                INSERT INTO bikes (bike_type_id, brand, frame_size, photo, price_per_day)
                VALUES (:bike_type_id, :brand, :frame_size, :photo, :price_per_day)
            ");
            $stmt->execute([
                ':bike_type_id'  => $_POST['bike_type_id'],
                ':brand'         => $_POST['brand'],
                ':frame_size'    => $_POST['frame_size'],
                ':photo'         => $naam,
                ':price_per_day' => $_POST['price_per_day'],
            ]);
            header('Location: index.php');
            exit;
        }
        $fout = 'De foto kon niet worden opgeslagen. Controleer de rechten van de map images.';
    }
}

// Fiets verwijderen (soft delete: deleted_at invullen)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verwijderen'])) {
    if (!$isAdmin) {
        header('Location: login.php');
        exit;
    }
    $stmt = $pdo->prepare("UPDATE bikes SET deleted_at = NOW() WHERE id = :id");
    $stmt->execute([':id' => $_POST['id']]);
    header('Location: index.php');
    exit;
}

// Fietsen ophalen (zonder verwijderde)
$stmt = $pdo->query("
    SELECT bikes.*, bike_types.name AS type_name
    FROM bikes
    JOIN bike_types ON bikes.bike_type_id = bike_types.id
    WHERE bikes.deleted_at IS NULL
    ORDER BY bikes.brand
");
$bikes = $stmt->fetchAll();

// Fietstypes voor het formulier
$types = $pdo->query("SELECT id, name FROM bike_types ORDER BY name")->fetchAll();

$paginaTitel = 'Fietsen - TrapKracht';
require 'header.php';
?>

<h2>Onze fietsen</h2>

<div class="bike-grid">
    <?php foreach ($bikes as $bike): ?>
        <?php
        $fotoBestand = basename($bike['photo']);
        $fotoBestaat = file_exists(__DIR__ . '/images/' . $fotoBestand);
        ?>
        <article class="bike-card">
            <a class="bike-link" href="bike.php?id=<?= $bike['id'] ?>">
                <?php if ($fotoBestaat): ?>
                    <img src="images/<?= htmlspecialchars($fotoBestand) ?>"
                         alt="<?= htmlspecialchars($bike['brand'] . ' ' . $bike['type_name']) ?>">
                <?php else: ?>
                    <div class="bike-noimg">Geen foto</div>
                <?php endif; ?>

                <div class="bike-info">
                    <h3><?= htmlspecialchars($bike['brand']) ?></h3>
                    <p class="bike-type"><?= htmlspecialchars($bike['type_name']) ?> &middot; <?= $bike['frame_size'] ?> cm</p>
                    <p class="bike-price">€ <?= number_format($bike['price_per_day'], 2, ',', '.') ?> per dag</p>
                </div>
            </a>

            <?php if ($isAdmin): ?>
                <form class="bike-admin" method="post" onsubmit="return confirm('Weet je het zeker?')">
                    <input type="hidden" name="id" value="<?= $bike['id'] ?>">
                    <button type="submit" name="verwijderen">Verwijder</button>
                </form>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>

<?php if ($isAdmin): ?>
<h2>Fiets toevoegen</h2>

<?php if ($fout): ?>
    <p class="fout"><?= htmlspecialchars($fout) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <p><label>Merk: <input type="text" name="brand" required></label></p>
    <p>
        <label>Type:
            <select name="bike_type_id">
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>
    <p><label>Framemaat (cm): <input type="number" name="frame_size" required></label></p>
    <p><label>Foto (JPG, PNG of WEBP, max 2 MB): <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required></label></p>
    <p><label>Prijs per dag: <input type="number" step="0.01" name="price_per_day" required></label></p>
    <button type="submit" name="toevoegen">Toevoegen</button>
</form>
<?php endif; ?>

<?php require 'footer.php'; ?>