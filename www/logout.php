<?php
session_start();

// Sessie leegmaken en vernietigen
$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;