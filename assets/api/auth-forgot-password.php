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

$email = $_POST['email'] ?? '';

echo json_encode($auth->forgotPassword($email, $mailer));
