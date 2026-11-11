<?php
require_once __DIR__ . '/../import/init.php';

$base     = rtrim($_ENV['APP_URL'] ?? '', '/');
$ticketId = (int) ($_POST['ticket_id'] ?? 0);
// Retour : la page du ticket, « Gérer vos tickets » (list) ou la gestion staff (manage, filtres conservés)
$redirect = match ($_POST['return'] ?? '') {
    'list'      => "{$base}/dashboard?page=tickets",
    'dashboard' => "{$base}/dashboard",
    'staff'  => "{$base}/dashboard?page=ticket-staff&id={$ticketId}",
    'manage' => (function () use ($base) {
        parse_str((string) ($_POST['return_query'] ?? ''), $q);
        $q = array_intersect_key($q, array_flip(['q', 'status', 'category', 'assignee', 'sort', 'p']));
        return "{$base}/dashboard?" . http_build_query(['page' => 'manage-tickets'] + $q);
    })(),
    default  => "{$base}/dashboard?page=ticket-view&id={$ticketId}",
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$base}/dashboard?page=tickets");
    exit;
}

if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Les fichiers envoyés sont trop volumineux.'];
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? "{$base}/dashboard?page=tickets"));
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

$user    = $auth->user();
$isStaff = $tickets->isStaff($user, $groupManager);

if (!$isStaff && !$groupManager->userCan($user, 'view-own-tickets')) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "Vous n'avez pas la permission de gérer vos tickets."];
    header("Location: {$base}/dashboard");
    exit;
}

$action      = (string) ($_POST['action'] ?? '');
$fromStaff   = $isStaff && ($_POST['return'] ?? '') === 'staff'; // action envoyée depuis la vue staff
$staffAction = in_array($action, ['assign', 'assign_self', 'staff_update', 'reopen', 'settings', 'note', 'note_delete', 'template_create', 'template_delete'], true)
            || !empty($_POST['resolve']);

if ($staffAction && !$isStaff) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => "Vous n'avez pas la permission de gérer les tickets."];
    header("Location: {$redirect}");
    exit;
}

$result = match ($action) {
    'reply'  => $tickets->reply($ticketId, $user, $isStaff, (string) ($_POST['message'] ?? ''), $_FILES['attachments'] ?? [], $fromStaff),
    'update' => $tickets->update($ticketId, $user, (string) ($_POST['subject'] ?? ''), (string) ($_POST['message'] ?? '')),
    'close'  => $tickets->close($ticketId, $user, $isStaff, $fromStaff),
    // Staff
    'assign_self'  => $tickets->assign($ticketId, $user['id']),
    'assign'       => $tickets->assign($ticketId, ($_POST['assignee'] ?? '') !== '' ? (string) $_POST['assignee'] : null),
    'staff_update' => $tickets->staffUpdate($ticketId, (string) ($_POST['subject'] ?? ''), (string) ($_POST['category'] ?? ''), (string) ($_POST['priority'] ?? '')),
    'reopen'       => $tickets->reopen($ticketId),
    // Vue staff
    'settings'        => $tickets->updateSettings($ticketId, $_POST, $user),
    'note'            => $tickets->addNote($ticketId, $user, (string) ($_POST['note'] ?? '')),
    'note_delete'     => $tickets->deleteNote((int) ($_POST['note_id'] ?? 0), $user),
    'template_create' => $tickets->createTemplate((string) ($_POST['template_title'] ?? ''), (string) ($_POST['template_body'] ?? ''), $user),
    'template_delete' => $tickets->deleteTemplate((int) ($_POST['template_id'] ?? 0)),
    default  => ['success' => false, 'message' => 'Action inconnue.'],
};

// « Envoyer et résoudre » : la réponse est envoyée, puis le ticket est marqué résolu
if ($action === 'reply' && $result['success'] && !empty($_POST['resolve'])) {
    $closed = $tickets->close($ticketId, $user, $isStaff, $fromStaff);
    $result = $closed['success']
        ? ['success' => true, 'message' => 'Réponse envoyée et ticket marqué comme résolu.']
        : ['success' => false, 'message' => 'Réponse envoyée, mais : ' . $closed['message']];
}

$_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
header("Location: {$redirect}");
exit;
