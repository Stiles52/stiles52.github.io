<?php
require_once __DIR__ . '/../import/init.php';

$base = rtrim($_ENV['APP_URL'] ?? '', '/');

// Retour vers la liste en conservant recherche / filtres / page (uniquement des paramètres connus → pas d'open redirect)
parse_str((string) ($_POST['return'] ?? ''), $returnQuery);
$returnQuery = array_intersect_key($returnQuery, array_flip(['q', 'group', 'status', 'sort', 'p']));
$redirect    = "{$base}/dashboard?" . http_build_query(['page' => 'users'] + $returnQuery);

// Action lancée depuis la fiche profil : retour sur cette fiche
if (($_POST['return_to'] ?? '') === 'profile' && preg_match('/^[0-9a-f-]{36}$/', (string) ($_POST['user_id'] ?? ''))) {
    $redirect = "{$base}/dashboard?page=profil-view&id=" . $_POST['user_id'];
}

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

if (!$groupManager->userCan($auth->user(), 'manage-users')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "Vous n'avez pas la permission de gérer les utilisateurs."];
    header("Location: {$base}/dashboard");
    exit;
}

$action = (string) ($_POST['action']  ?? '');
$userId = (string) ($_POST['user_id'] ?? '');

$result = match ($action) {
    'create'           => $users->create($_POST),
    'update'           => $users->update($userId, $_POST),
    'sanction'         => $sanctions->issue($userId, (string) ($_POST['type'] ?? ''), (string) ($_POST['reason'] ?? ''), (string) ($_POST['duration'] ?? ''), $auth->user()),
    'revoke'           => $sanctions->revoke($userId, $auth->user()),
    default            => ['success' => false, 'message' => 'Action inconnue.'],
};

$_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
header("Location: {$redirect}");
exit;
