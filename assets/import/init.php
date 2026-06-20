<?php

// Charge le .env et vérifie les variables obligatoires
require_once __DIR__ . '/../../config.php';

// --- Affichage des erreurs selon l'environnement ---
if (($_ENV['APP_ENV'] ?? 'production') === 'development' && ($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// --- Sécurité session ---
ini_set('session.use_strict_mode',  '1'); // prévention session fixation
ini_set('session.use_only_cookies', '1'); // interdit PHPSESSID en URL

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120) * 60,
        'path'     => '/',
        'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// --- Chargement des classes ---
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Mailer.php';
require_once __DIR__ . '/Auth.php';

// --- Instances globales disponibles dans toute page qui inclut ce fichier ---
$db     = Database::getInstance();
$auth   = new Auth($db);
$mailer = new Mailer();
