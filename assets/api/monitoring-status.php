<?php
require_once __DIR__ . '/../../assets/import/init.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!$auth->check() || !$groupManager->userCan($auth->user(), 'view-monitoring')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => "Vous n'avez pas la permission d'accéder au monitoring."]);
    exit;
}

// Libère le verrou de session : la vérification HTTP peut prendre quelques secondes
session_write_close();

$monitor->runCheck();

echo json_encode(['success' => true] + $monitor->status());
