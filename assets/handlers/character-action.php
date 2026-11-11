<?php
require_once __DIR__ . '/../import/init.php';

$base          = rtrim($_ENV['APP_URL'] ?? '', '/');
$characterId   = (int) ($_POST['character_id'] ?? 0);
$candidatureId = (int) ($_POST['candidature_id'] ?? 0);

// Retour selon l'origine de l'action (paramètres connus uniquement → pas d'open redirect)
$redirect = match ($_POST['return'] ?? '') {
    'character'  => "{$base}/dashboard?page=personnage&id={$characterId}",
    'parameters' => "{$base}/dashboard?page=parameters",
    'form'       => "{$base}/dashboard?page=personnage-form" . ($characterId ? "&id={$characterId}" : ''),
    'staff'      => "{$base}/dashboard?page=candidature-staff&id={$candidatureId}",
    'staff_list' => (function () use ($base) {
        parse_str((string) ($_POST['return_query'] ?? ''), $q);
        $q = array_intersect_key($q, array_flip(['q', 'status', 'type', 'sort', 'p']));
        return "{$base}/dashboard?" . http_build_query(['page' => 'candidatures'] + $q);
    })(),
    default      => "{$base}/dashboard?page=personnages",
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$base}/dashboard?page=personnages");
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

$user        = $auth->user();
$action      = (string) ($_POST['action'] ?? '');
$staffView   = $groupManager->userCan($user, 'view-candidatures', 'manage-candidatures');
$staffManage = $groupManager->userCan($user, 'manage-candidatures');

$staffOnly = ['decide', 'cancel', 'set_level', 'set_max'];
if (in_array($action, $staffOnly, true) && !$staffManage) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "Vous n'avez pas la permission de gérer les candidatures."];
    header("Location: {$redirect}");
    exit;
}

$result = match ($action) {
    // Joueur
    'submit'     => $characters->submit($user, $characterId ?: null, $_POST),
    'set_active' => $characters->setActive($user, $characterId, ($_POST['active'] ?? '') === '1'),
    'delete'     => $characters->delete($user, $characterId),
    'reply'      => $characters->reply($candidatureId, $user, $staffView, ($_POST['return'] ?? '') === 'staff', (string) ($_POST['message'] ?? '')),
    // Staff
    'decide'     => $characters->decide($candidatureId, (string) ($_POST['decision'] ?? ''), (string) ($_POST['comment'] ?? ''), $user),
    'cancel'     => $characters->cancelDecision($candidatureId, $user),
    'set_level'  => $characters->setLevel($characterId, (string) ($_POST['level'] ?? '')),
    'set_max'    => $characters->setMaxCharacters((string) ($_POST['user_id'] ?? ''), (int) ($_POST['max_characters'] ?? 0)),
    default      => ['success' => false, 'message' => 'Action inconnue.'],
};

if ($action === 'submit') {
    if ($result['success']) {
        unset($_SESSION['character_form']);
        $redirect = "{$base}/dashboard?page=personnage&id={$result['character_id']}";
    } else {
        // Le questionnaire est long : on conserve les réponses pour le réafficher
        $_SESSION['character_form'] = array_diff_key($_POST, array_flip(['csrf_token', 'action', 'return']));
        $redirect = "{$base}/dashboard?page=personnage-form" . ($characterId ? "&id={$characterId}" : '');
    }
}

$_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
header("Location: {$redirect}");
exit;
