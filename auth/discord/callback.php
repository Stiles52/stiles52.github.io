<?php
/**
 * Retour OAuth2 de Discord (DISCORD_REDIRECT_URI = ${APP_URL}/auth/discord/callback).
 * Lie le compte Discord au compte du site connecté.
 */

// Les cookies de session sont en SameSite=Strict : ils ne sont pas envoyés sur une navigation venant de discord.com.
// Premier passage : on rebondit vers la même URL depuis notre propre page (navigation same-site → cookies envoyés).
// Ce passage ne doit PAS démarrer de session (un nouveau cookie de session écraserait celui du joueur).
if (!isset($_GET['hop'])) {
    $relay = '?' . http_build_query(array_intersect_key($_GET, array_flip(['code', 'state', 'error'])) + ['hop' => 1]);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    header('Referrer-Policy: no-referrer');
    echo '<!DOCTYPE html><meta charset="utf-8"><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($relay, ENT_QUOTES) . '">'
       . '<p style="font-family:monospace;color:#22d3ee;background:#000">Liaison Discord en cours…</p>';
    exit;
}

require_once __DIR__ . '/../../assets/import/init.php';

$base  = rtrim($_ENV['APP_URL'] ?? '', '/');
$oauth = $_SESSION['discord_oauth'] ?? null;
unset($_SESSION['discord_oauth']);

if (!$auth->check()) {
    header("Location: {$base}/login");
    exit;
}

$back = $oauth['back'] ?? "{$base}/dashboard?page=profil";

// Protection CSRF : le state doit correspondre à celui généré au départ
if (!$oauth || !hash_equals($oauth['state'], (string) ($_GET['state'] ?? ''))) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Liaison Discord expirée ou invalide. Réessayez.'];
    header("Location: {$back}");
    exit;
}

if (isset($_GET['error']) || empty($_GET['code'])) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Liaison Discord annulée.'];
    header("Location: {$back}");
    exit;
}

$identity = $discord->fetchIdentity((string) $_GET['code']);
$result   = is_string($identity)
    ? ['success' => false, 'message' => $identity]
    : $discord->link($auth->user()['id'], $identity);

$_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
header("Location: {$back}");
exit;
