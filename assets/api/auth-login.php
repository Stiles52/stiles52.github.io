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

$identifier = $_POST['identifier'] ?? '';
$password   = $_POST['password']   ?? '';
$remember   = !empty($_POST['remember']);

$result = $auth->login($identifier, $password, $remember);

echo json_encode($result);
