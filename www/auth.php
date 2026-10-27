<?php
// Start de sessie (moet gebeuren voordat er iets naar de browser wordt gestuurd)
session_start();

// Is er iemand ingelogd?
function isIngelogd(): bool
{
    return isset($_SESSION['user_id']);
}

// Is de ingelogde gebruiker een admin?
function isAdmin(): bool
{
    return isIngelogd() && ($_SESSION['role'] ?? '') === 'admin';
}

// Stuur niet-admins naar de loginpagina
function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}