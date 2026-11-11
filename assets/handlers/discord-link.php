<?php
require_once __DIR__ . '/../import/init.php';

$base = rtrim($_ENV['APP_URL'] ?? '', '/');

if (!$auth->check()) {
    header("Location: {$base}/login");
    exit;
}

// Page de retour après la liaison (valeurs connues uniquement)
$returns = ['profil' => '/dashboard?page=profil', 'form' => '/dashboard?page=personnage-form', 'parameters' => '/dashboard?page=parameters'];
$from    = (string) ($_REQUEST['from'] ?? 'profil');
$back    = $base . ($returns[$from] ?? $returns['profil']);

// Déliaison (POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token de sécurité invalide. Rechargez la page.'];
    } else {
        $_SESSION['flash'] = ['type' => 'success', 'message' => $discord->unlink($auth->user()['id'])['message']];
    }
    header("Location: {$back}");
    exit;
}

// Liaison : redirection vers Discord
if (!Discord::isConfigured()) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "La liaison Discord n'est pas encore configurée sur le site. Contactez le staff."];
    header("Location: {$back}");
    exit;
}

$_SESSION['discord_oauth'] = ['state' => bin2hex(random_bytes(16)), 'back' => $back];
header('Location: ' . $discord->authorizeUrl($_SESSION['discord_oauth']['state']));
exit;
