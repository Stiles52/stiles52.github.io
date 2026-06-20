<?php
require_once __DIR__ . '/../import/init.php';

$base = rtrim($_ENV['APP_URL'] ?? '', '/');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$base}/login");
    exit;
}

if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token de sécurité invalide. Rechargez la page.'];
    header("Location: {$base}/login");
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password']        ?? '';
$remember   = !empty($_POST['remember']);

$result = $auth->login($identifier, $password, $remember);

if (!$result['success']) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => $result['message']];
    header("Location: {$base}/login");
    exit;
}

header("Location: {$base}/dashboard");
exit;
