<?php
require_once __DIR__ . '/../import/init.php';

$base = rtrim($_ENV['APP_URL'] ?? '', '/');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$base}/register");
    exit;
}

if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token de sécurité invalide. Rechargez la page.'];
    header("Location: {$base}/register");
    exit;
}

$pseudo    = trim($_POST['pseudo']   ?? '');
$email     = trim($_POST['email']    ?? '');
$password  = $_POST['password']      ?? '';
$password2 = $_POST['password2']     ?? '';

$result = $auth->register($pseudo, $email, $password, $password2, $mailer);

$_SESSION['flash'] = [
    'type'    => $result['success'] ? 'success' : 'error',
    'message' => $result['message'],
];

header("Location: {$base}/register");
exit;
