<?php
// Verbinding met de database via PDO
$host    = 'mariadb';
$dbname  = 'Fietsverhuur';
$user    = 'root';
$pass    = 'password';
$charset = 'utf8mb4';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database verbinding mislukt: ' . $e->getMessage());
}