<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dotenv->required([
    'APP_ENV',
    'APP_SECRET_KEY',
    'DB_HOST',
    'DB_DATABASE',
    'DB_USERNAME',
    'DISCORD_WEBHOOK',
]);
