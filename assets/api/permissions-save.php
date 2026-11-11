<?php
require_once __DIR__ . '/../../assets/import/init.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide. Rechargez la page.']);
    exit;
}

if (!$auth->check() || !$groupManager->userCan($auth->user(), 'manage-permissions')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => "Vous n'avez pas la permission de gérer les permissions."]);
    exit;
}

// matrix = {"<groupId>": ["permission", ...], ...}
$matrix = json_decode($_POST['matrix'] ?? '', true);
if (!is_array($matrix)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Matrice de permissions invalide.']);
    exit;
}

echo json_encode($groupManager->saveMatrix($matrix, $auth->user()));
