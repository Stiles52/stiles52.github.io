<?php
require_once __DIR__ . '/../import/init.php';

$base     = rtrim($_ENV['APP_URL'] ?? '', '/');
$redirect = "{$base}/dashboard?page=parameters";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$redirect}");
    exit;
}

if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token de sécurité invalide. Rechargez la page.'];
    header("Location: {$redirect}");
    exit;
}

if (!$auth->check()) {
    header("Location: {$base}/login");
    exit;
}

$userId = $auth->user()['id'];
$result = $users->updateOwnSettings($userId, $_POST);

if ($result['success']) {
    if ($result['password_changed']) {
        $auth->revokeOtherSessions($userId);
        $result['message'] .= ' Vos autres sessions ont été déconnectées.';
    }
    unset($_SESSION['settings_form']);
} else {
    // Réaffiche le formulaire en mode édition avec la saisie (jamais les mots de passe)
    $_SESSION['settings_form'] = array_intersect_key($_POST, array_flip(['pseudo', 'email', 'minecraft_pseudo', 'email_notifications']));
}

$_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
header("Location: {$redirect}");
exit;
