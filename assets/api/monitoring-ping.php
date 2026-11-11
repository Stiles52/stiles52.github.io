<?php
/**
 * Vérification de disponibilité à appeler par une tâche planifiée (cron / Planificateur de tâches),
 * pour que l'uptime soit mesuré même quand personne ne consulte le monitoring :
 *
 *   curl -s "https://origin-rp.fr/assets/api/monitoring-ping?key=<APP_SECRET_KEY>"
 */
require_once __DIR__ . '/../../assets/import/init.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$secret = (string) ($_ENV['APP_SECRET_KEY'] ?? '');
if ($secret === '' || !hash_equals($secret, (string) ($_GET['key'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Clé invalide.']);
    exit;
}

$result = $monitor->runCheck();

echo json_encode(['success' => true, 'checked' => $result !== null, 'result' => $result]);
