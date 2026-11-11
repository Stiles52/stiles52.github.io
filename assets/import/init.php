<?php

// Charge le .env et vérifie les variables obligatoires
require_once __DIR__ . '/../../config.php';

// --- Fuseau horaire (PHP et MySQL doivent être alignés : voir Database) ---
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Paris');

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
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/SanctionManager.php';
require_once __DIR__ . '/UserManager.php';
require_once __DIR__ . '/GroupManager.php';
require_once __DIR__ . '/Monitor.php';

// --- Instances globales disponibles dans toute page qui inclut ce fichier ---
$db     = Database::getInstance();
$auth   = new Auth($db);
$mailer = new Mailer();
$users  = new UserManager($db);
$groupManager = new GroupManager($db);
$sanctions    = new SanctionManager($db);
$monitor      = new Monitor($db);
