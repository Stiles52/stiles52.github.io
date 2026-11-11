<?php
require_once __DIR__ . '/../import/init.php';

if (!$auth->check()) {
    http_response_code(403);
    exit;
}

$user       = $auth->user();
$attachment = $tickets->attachment((int) ($_GET['id'] ?? 0), $user, $tickets->isStaff($user, $groupManager));

if (!$attachment) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Fichier introuvable.';
    exit;
}

// Toujours en téléchargement (jamais interprété par le navigateur)
$filename = preg_replace('/[^\w.\- ]+/u', '_', $attachment['original_name']);
header('Content-Type: ' . $attachment['mime']);
header('Content-Length: ' . filesize($attachment['path']));
header('Content-Disposition: attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($attachment['original_name']));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($attachment['path']);
