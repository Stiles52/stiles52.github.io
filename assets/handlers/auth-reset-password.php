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

$token     = trim($_POST['token']     ?? '');
$password  = $_POST['password']       ?? '';
$password2 = $_POST['password2']      ?? '';

if ($password !== $password2) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Les mots de passe ne correspondent pas.'];
    header("Location: {$base}/reset-password?token=" . urlencode($token));
    exit;
}

$result = $auth->resetPassword($token, $password);

if (!$result['success']) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => $result['message']];
    header("Location: {$base}/reset-password?token=" . urlencode($token));
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'message' => $result['message']];
header("Location: {$base}/login");
exit;
