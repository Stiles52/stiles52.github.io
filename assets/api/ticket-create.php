<?php
require_once __DIR__ . '/../../assets/import/init.php';

header('Content-Type: application/json; charset=utf-8');

function respond(int $code, array $data): never
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Méthode non autorisée.']);
}

// Requête plus grosse que post_max_size : PHP vide $_POST et $_FILES
if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    respond(413, ['success' => false, 'message' => 'Les fichiers envoyés sont trop volumineux.']);
}

if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    respond(403, ['success' => false, 'message' => 'Token de sécurité invalide. Rechargez la page.']);
}

if (!$auth->check()) {
    respond(401, ['success' => false, 'message' => 'Votre session a expiré. Reconnectez-vous.']);
}

if (!$groupManager->userCan($auth->user(), 'create-ticket')) {
    respond(403, ['success' => false, 'message' => "Vous n'avez pas la permission de créer un ticket."]);
}

$result = $tickets->create($auth->user(), (string) ($_POST['category'] ?? ''), $_POST, $_FILES['attachments'] ?? []);

if ($result['success']) {
    $result['redirect'] = './dashboard?page=ticket-view&id=' . $result['id'];
}

respond($result['success'] ? 200 : 422, $result);
