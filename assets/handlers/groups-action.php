<?php
require_once __DIR__ . '/../import/init.php';

$base     = rtrim($_ENV['APP_URL'] ?? '', '/');
$redirect = "{$base}/dashboard?page=groups";

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

if (!$groupManager->userCan($auth->user(), 'manage-groups')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "Vous n'avez pas la permission de gérer les groupes."];
    header("Location: {$base}/dashboard");
    exit;
}

$groupId = (int) ($_POST['group_id'] ?? 0);

$result = match ($_POST['action'] ?? '') {
    'create' => $groupManager->create($_POST),
    'update' => $groupManager->update($groupId, $_POST, $auth->user()),
    'delete' => $groupManager->delete($groupId, $auth->user()),
    default  => ['success' => false, 'message' => 'Action inconnue.'],
};

$_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
header("Location: {$redirect}");
exit;
