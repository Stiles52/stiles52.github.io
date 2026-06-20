<?php
require_once __DIR__ . '/../import/init.php';

$base = rtrim($_ENV['APP_URL'] ?? '', '/');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$base}/forgotten-password");
    exit;
}

if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token de sécurité invalide. Rechargez la page.'];
    header("Location: {$base}/forgotten-password");
    exit;
}

$email  = trim($_POST['email'] ?? '');
$result = $auth->forgotPassword($email, $mailer);

// Toujours type 'success' côté flash (anti-énumération) ;
// le message diffère selon MAIL_DRIVER (mode dev : lien direct inclus).
$_SESSION['flash'] = ['type' => 'success', 'message' => $result['message']];

header("Location: {$base}/forgotten-password");
exit;
