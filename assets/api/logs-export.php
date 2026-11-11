<?php
require_once __DIR__ . '/../../assets/import/init.php';

if (!$auth->check() || !$groupManager->userCan($auth->user(), 'view-logs')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Vous n'avez pas la permission d'exporter les logs.";
    exit;
}

// Mêmes filtres que la page (recherche, type, période, tri)
$filters = Logger::filtersFrom($_GET);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="origin-logs-' . date('Y-m-d_His') . '.csv"');
header('Cache-Control: no-store');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
fputcsv($out, ['Horodatage', 'Utilisateur', 'Type', 'Détails', 'IP'], ';');

foreach (Logger::export($filters) as $log) {
    $details = Logger::plainDetails($log);
    fputcsv($out, [
        date('d/m/Y H:i:s', strtotime($log['created_at'])),
        $log['username'] ?? 'Système',
        Logger::TYPES[$log['type']]['label'] ?? $log['type'],
        // Neutralise l'injection de formules dans les tableurs
        preg_match('/^[=+\-@]/', $details) ? "'" . $details : $details,
        Logger::maskIp($log['ip_address']),
    ], ';');
}

fclose($out);
