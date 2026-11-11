<?php

/**
 * Liaison d'un compte Discord à un compte du site (OAuth2, scope « identify »).
 * Configuration .env : DISCORD_CLIENT_ID, DISCORD_CLIENT_SECRET, DISCORD_REDIRECT_URI
 * (l'URI de redirection doit aussi être déclarée dans le portail développeur Discord).
 */
class Discord
{
    private const AUTHORIZE_URL = 'https://discord.com/oauth2/authorize';
    private const TOKEN_URL     = 'https://discord.com/api/oauth2/token';
    private const ME_URL        = 'https://discord.com/api/users/@me';

    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public static function isConfigured(): bool
    {
        return ($_ENV['DISCORD_CLIENT_ID'] ?? '') !== ''
            && ($_ENV['DISCORD_CLIENT_SECRET'] ?? '') !== ''
            && ($_ENV['DISCORD_REDIRECT_URI'] ?? '') !== '';
    }

    /** En développement, sans application Discord configurée, l'exigence de liaison est ignorée. */
    public static function requirementSkipped(): bool
    {
        return !self::isConfigured() && ($_ENV['APP_ENV'] ?? 'production') === 'development';
    }

    public function authorizeUrl(string $state): string
    {
        return self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id'     => $_ENV['DISCORD_CLIENT_ID'],
            'redirect_uri'  => $_ENV['DISCORD_REDIRECT_URI'],
            'response_type' => 'code',
            'scope'         => 'identify',
            'state'         => $state,
            'prompt'        => 'consent',
        ]);
    }

    /** Échange le code OAuth contre l'identité Discord. ['id' => …, 'username' => …] ou message d'erreur (string). */
    public function fetchIdentity(string $code): array|string
    {
        $token = $this->request(self::TOKEN_URL, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id'     => $_ENV['DISCORD_CLIENT_ID'],
                'client_secret' => $_ENV['DISCORD_CLIENT_SECRET'],
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $_ENV['DISCORD_REDIRECT_URI'],
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        if (empty($token['access_token'])) {
            return 'Discord a refusé la connexion. Réessayez.';
        }

        $me = $this->request(self::ME_URL, [CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token['access_token']]]);
        if (empty($me['id'])) {
            return 'Impossible de récupérer votre compte Discord.';
        }
        return ['id' => (string) $me['id'], 'username' => (string) ($me['global_name'] ?? $me['username'] ?? '')];
    }

    public function link(string $userId, array $identity): array
    {
        $taken = $this->db->fetchOne('SELECT pseudo FROM users WHERE discord_id = ? AND id <> ?', [$identity['id'], $userId]);
        if ($taken) {
            return ['success' => false, 'message' => 'Ce compte Discord est déjà lié à un autre compte du site.'];
        }
        $this->db->query(
            'UPDATE users SET discord_id = ?, discord_username = ?, discord_linked_at = NOW() WHERE id = ?',
            [$identity['id'], mb_substr($identity['username'], 0, 64), $userId]
        );
        Logger::log('modification', 'Compte Discord lié : ', $identity['username'], 'cyan');
        return ['success' => true, 'message' => "Compte Discord « {$identity['username']} » lié."];
    }

    public function unlink(string $userId): array
    {
        $this->db->query('UPDATE users SET discord_id = NULL, discord_username = NULL, discord_linked_at = NULL WHERE id = ?', [$userId]);
        Logger::log('modification', 'Compte Discord délié');
        return ['success' => true, 'message' => 'Compte Discord délié.'];
    }

    private function request(string $url, array $options): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, $options + [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8]);
        $body = curl_exec($ch);
        if ($body === false) {
            error_log('[Discord] ' . curl_error($ch));
            return [];
        }
        return json_decode((string) $body, true) ?: [];
    }
}
