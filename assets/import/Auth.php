<?php

class Auth
{
    private Database $db;
    private ?array   $currentUser = null;

    private const COOKIE_NAME  = 'origin_session';
    private const TTL_DEFAULT  = 3600;    // 1 heure
    private const TTL_REMEMBER = 2592000; // 30 jours
    private const TTL_VERIFY   = 86400;   // 24h — vérification e-mail
    private const TTL_RESET    = 3600;    // 1h  — réinitialisation mot de passe
    private const BCRYPT_COST  = 12;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    // ----------------------------------------------------------------
    //  INSCRIPTION
    // ----------------------------------------------------------------

    public function register(string $pseudo, string $email, string $password, Mailer $mailer): array
    {
        $pseudo = trim($pseudo);
        $email  = strtolower(trim($email));

        if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
            return $this->err('Le pseudonyme doit contenir entre 3 et 50 caractères.');
        }
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $pseudo)) {
            return $this->err('Le pseudonyme ne peut contenir que des lettres, chiffres, tirets et underscores.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->err('Adresse e-mail invalide.');
        }
        if (strlen($password) < 8) {
            return $this->err('Le mot de passe doit contenir au moins 8 caractères.');
        }

        if ($this->db->fetchOne('SELECT id FROM users WHERE email = ?', [$email])) {
            return $this->err('Cette adresse e-mail est déjà utilisée.');
        }
        if ($this->db->fetchOne('SELECT id FROM users WHERE pseudo = ?', [$pseudo])) {
            return $this->err('Ce pseudonyme est déjà pris.');
        }

        $hash   = password_hash($password, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST]);
        $userId = $this->generateUuid();

        $this->db->query(
            'INSERT INTO users (id, pseudo, email, password) VALUES (?, ?, ?, ?)',
            [$userId, $pseudo, $email, $hash]
        );

        // En mode dev sans e-mail : l'adresse est auto-vérifiée, pas besoin de cliquer un lien
        if ($this->isMailSkipped()) {
            $this->db->query('UPDATE users SET email_verified_at = NOW() WHERE id = ?', [$userId]);
            return ['success' => true, 'message' => '[DEV] Compte créé et e-mail auto-vérifié. Vous pouvez vous connecter.'];
        }

        $this->sendVerificationEmail($userId, $email, $pseudo, $mailer);

        return ['success' => true, 'message' => 'Compte créé ! Vérifiez votre e-mail pour activer votre compte.'];
    }

    // ----------------------------------------------------------------
    //  VÉRIFICATION E-MAIL
    // ----------------------------------------------------------------

    private function sendVerificationEmail(string $userId, string $email, string $pseudo, Mailer $mailer): void
    {
        $this->db->query('DELETE FROM email_verifications WHERE user_id = ?', [$userId]);

        $id        = $this->generateUuid();
        $token     = $this->generateToken(32);
        $expiresAt = date('Y-m-d H:i:s', time() + self::TTL_VERIFY);

        $this->db->query(
            'INSERT INTO email_verifications (id, user_id, token, expires_at) VALUES (?, ?, ?, ?)',
            [$id, $userId, $token, $expiresAt]
        );

        $link    = rtrim($_ENV['APP_URL'], '/') . '/verify-email?token=' . $token;
        $subject = '[ORIGIN] Vérifiez votre adresse e-mail';

        $mailer->send($email, $subject, $this->emailTemplate($subject, "
            <p>Bonjour <strong>" . htmlspecialchars($pseudo) . "</strong>,</p>
            <p>Cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail.<br>
            Ce lien est valable <strong>24 heures</strong>.</p>
            <p style='text-align:center;margin:32px 0;'>
                <a href='" . htmlspecialchars($link) . "'
                   style='background:#22d3ee;color:#000;padding:12px 28px;text-decoration:none;
                          font-weight:bold;border-radius:4px;letter-spacing:1px;'>
                    VÉRIFIER MON E-MAIL
                </a>
            </p>
            <p style='font-size:12px;color:#666;'>
                Si vous n'avez pas créé de compte sur ORIGIN RP, ignorez cet e-mail.
            </p>
        "));
    }

    public function verifyEmail(string $token): array
    {
        if (!ctype_alnum($token)) {
            return $this->err('Token invalide.');
        }

        $row = $this->db->fetchOne(
            'SELECT user_id FROM email_verifications WHERE token = ? AND expires_at > NOW()',
            [$token]
        );

        if (!$row) {
            return $this->err('Ce lien est invalide ou a expiré.');
        }

        $this->db->transaction(function (Database $db) use ($row, $token) {
            $db->query('UPDATE users SET email_verified_at = NOW() WHERE id = ?', [$row['user_id']]);
            $db->query('DELETE FROM email_verifications WHERE token = ?', [$token]);
        });

        return ['success' => true, 'message' => 'E-mail vérifié. Vous pouvez maintenant vous connecter.'];
    }

    // ----------------------------------------------------------------
    //  CONNEXION
    // ----------------------------------------------------------------

    public function login(string $identifier, string $password, bool $remember): array
    {
        $identifier = trim($identifier);

        $user = $this->db->fetchOne(
            'SELECT * FROM users WHERE email = ? OR pseudo = ? LIMIT 1',
            [$identifier, $identifier]
        );

        // Toujours appeler password_verify pour éviter les timing attacks (même si user introuvable)
        $dummyHash = '$2y$12$invalidhashpadxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $hashToCheck = $user ? $user['password'] : $dummyHash;

        if (!password_verify($password, $hashToCheck) || !$user) {
            return $this->err('Identifiant ou mot de passe incorrect.');
        }

        if ($user['email_verified_at'] === null) {
            return $this->err('Veuillez vérifier votre adresse e-mail avant de vous connecter.');
        }

        // Régénère l'ID de session PHP → prévention session fixation
        session_regenerate_id(true);

        $ttl   = $remember ? self::TTL_REMEMBER : self::TTL_DEFAULT;
        $token = $this->createSessionToken($user['id'], $remember);
        $this->setSessionCookie($token, $ttl);

        $this->currentUser = $user;

        // Met à jour le hash si le coût bcrypt a changé depuis la dernière connexion
        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST]);
            $this->db->query('UPDATE users SET password = ? WHERE id = ?', [$newHash, $user['id']]);
        }

        return ['success' => true, 'message' => 'Connexion réussie.'];
    }

    // ----------------------------------------------------------------
    //  DÉCONNEXION
    // ----------------------------------------------------------------

    public function logout(): void
    {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        if ($token && ctype_alnum($token)) {
            $this->db->query('DELETE FROM user_sessions WHERE token = ?', [$token]);
        }

        $this->clearSessionCookie();
        $this->currentUser = null;

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // ----------------------------------------------------------------
    //  VÉRIFICATION DE SESSION ACTIVE
    // ----------------------------------------------------------------

    public function check(): bool
    {
        if ($this->currentUser !== null) {
            return true;
        }

        $token = $_COOKIE[self::COOKIE_NAME] ?? null;

        // Validation format : un token valide est uniquement hexadécimal
        if (!$token || !ctype_alnum($token) || strlen($token) !== 128) {
            return false;
        }

        $row = $this->db->fetchOne(
            'SELECT u.id, u.pseudo, u.email, u.email_verified_at, s.remember, s.expires_at
             FROM user_sessions s
             JOIN users u ON u.id = s.user_id
             WHERE s.token = ? AND s.expires_at > NOW()
             LIMIT 1',
            [$token]
        );

        if (!$row) {
            $this->clearSessionCookie();
            return false;
        }

        $this->currentUser = $row;
        return true;
    }

    public function user(): ?array
    {
        return $this->currentUser;
    }

    // ----------------------------------------------------------------
    //  MOT DE PASSE OUBLIÉ
    // ----------------------------------------------------------------

    public function forgotPassword(string $email, Mailer $mailer): array
    {
        $email = strtolower(trim($email));

        $user = $this->db->fetchOne(
            'SELECT id, pseudo FROM users WHERE email = ? LIMIT 1',
            [$email]
        );

        // Réponse identique que l'e-mail existe ou non → anti-énumération de comptes
        $genericResponse = ['success' => true, 'message' => 'Si cet e-mail est enregistré, vous recevrez un lien de réinitialisation.'];

        if (!$user) {
            return $genericResponse;
        }

        $this->db->query('DELETE FROM password_resets WHERE user_id = ?', [$user['id']]);

        $id        = $this->generateUuid();
        $token     = $this->generateToken(32);
        $expiresAt = date('Y-m-d H:i:s', time() + self::TTL_RESET);

        $this->db->query(
            'INSERT INTO password_resets (id, user_id, token, expires_at) VALUES (?, ?, ?, ?)',
            [$id, $user['id'], $token, $expiresAt]
        );

        $link    = rtrim($_ENV['APP_URL'], '/') . '/reset-password?token=' . $token;
        $subject = '[ORIGIN] Réinitialisation de votre mot de passe';

        // En mode dev sans e-mail : on retourne le lien directement dans la réponse
        if ($this->isMailSkipped()) {
            return ['success' => true, 'message' => '[DEV] Lien de réinitialisation : ' . $link];
        }

        $mailer->send($email, $subject, $this->emailTemplate($subject, "
            <p>Bonjour <strong>" . htmlspecialchars($user['pseudo']) . "</strong>,</p>
            <p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe.<br>
            Ce lien est valable <strong>1 heure</strong>.</p>
            <p style='text-align:center;margin:32px 0;'>
                <a href='" . htmlspecialchars($link) . "'
                   style='background:#22d3ee;color:#000;padding:12px 28px;text-decoration:none;
                          font-weight:bold;border-radius:4px;letter-spacing:1px;'>
                    RÉINITIALISER MON MOT DE PASSE
                </a>
            </p>
            <p style='font-size:12px;color:#666;'>
                Si vous n'avez pas fait cette demande, ignorez cet e-mail. Votre mot de passe ne sera pas modifié.
            </p>
        "));

        return $genericResponse;
    }

    // ----------------------------------------------------------------
    //  RÉINITIALISATION DU MOT DE PASSE
    // ----------------------------------------------------------------

    /**
     * Vérifie si un token de reset est valide (pour pré-valider côté page).
     */
    public function isResetTokenValid(string $token): bool
    {
        if (!ctype_alnum($token)) {
            return false;
        }
        return (bool) $this->db->fetchOne(
            'SELECT id FROM password_resets WHERE token = ? AND expires_at > NOW()',
            [$token]
        );
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        if (!ctype_alnum($token)) {
            return $this->err('Token invalide.');
        }
        if (strlen($newPassword) < 8) {
            return $this->err('Le mot de passe doit contenir au moins 8 caractères.');
        }

        $row = $this->db->fetchOne(
            'SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()',
            [$token]
        );

        if (!$row) {
            return $this->err('Ce lien est invalide ou a expiré.');
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST]);

        $this->db->transaction(function (Database $db) use ($row, $token, $hash) {
            $db->query('UPDATE users SET password = ? WHERE id = ?', [$hash, $row['user_id']]);
            $db->query('DELETE FROM password_resets WHERE token = ?', [$token]);
            // Invalide toutes les sessions actives : l'utilisateur a changé de mot de passe
            $db->query('DELETE FROM user_sessions WHERE user_id = ?', [$row['user_id']]);
        });

        return ['success' => true, 'message' => 'Mot de passe mis à jour. Vous pouvez vous connecter.'];
    }

    // ----------------------------------------------------------------
    //  PROTECTION CSRF
    // ----------------------------------------------------------------

    /**
     * Génère un token CSRF stateless :
     *   base64(payload_json) . "." . hmac_sha256(payload, APP_SECRET_KEY)
     * Aucune session requise. Valide 2 heures.
     */
    public static function generateCsrfToken(): string
    {
        $payload = base64_encode(json_encode([
            'ts'    => time(),
            'nonce' => bin2hex(random_bytes(8)),
        ]));
        $sig = hash_hmac('sha256', $payload, $_ENV['APP_SECRET_KEY']);
        return $payload . '.' . $sig;
    }

    public static function validateCsrfToken(string $submitted): bool
    {
        if (empty($submitted) || substr_count($submitted, '.') !== 1) {
            return false;
        }

        [$payload, $sig] = explode('.', $submitted, 2);

        // Vérifie la signature (hash_equals = temps constant)
        $expectedSig = hash_hmac('sha256', $payload, $_ENV['APP_SECRET_KEY']);
        if (!hash_equals($expectedSig, $sig)) {
            return false;
        }

        // Vérifie que le token n'a pas expiré (2 heures max)
        $data = json_decode(base64_decode($payload), true);
        return isset($data['ts']) && time() - (int) $data['ts'] < 7200;
    }

    // ----------------------------------------------------------------
    //  UTILITAIRES PRIVÉS
    // ----------------------------------------------------------------

    private function createSessionToken(string $userId, bool $remember): string
    {
        // 64 bytes = 128 chars hexadécimaux → entropie 512 bits
        $id        = $this->generateUuid();
        $token     = $this->generateToken(64);
        $ttl       = $remember ? self::TTL_REMEMBER : self::TTL_DEFAULT;
        $expiresAt = date('Y-m-d H:i:s', time() + $ttl);

        $this->db->query(
            'INSERT INTO user_sessions (id, user_id, token, ip_address, user_agent, remember, expires_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $id,
                $userId,
                $token,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
                $remember ? 1 : 0,
                $expiresAt,
            ]
        );

        return $token;
    }

    private function isMailSkipped(): bool
    {
        return ($_ENV['MAIL_DRIVER'] ?? 'smtp') === 'none';
    }

    private function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant RFC 4122
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function generateToken(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    private function setSessionCookie(string $token, int $ttl): void
    {
        setcookie(self::COOKIE_NAME, $token, [
            'expires'  => time() + $ttl,
            'path'     => '/',
            'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    private function clearSessionCookie(): void
    {
        setcookie(self::COOKIE_NAME, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    private function err(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }

    private function emailTemplate(string $title, string $content): string
    {
        $appName = htmlspecialchars($_ENV['APP_NAME'] ?? 'ORIGIN RP');
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>$title</title></head>
<body style="margin:0;padding:0;background:#000;font-family:monospace;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#000;">
  <tr><td align="center" style="padding:40px 20px;">
    <table width="580" cellpadding="0" cellspacing="0"
           style="background:#0a0a0a;border:1px solid rgba(34,211,238,0.2);">
      <tr>
        <td style="padding:24px 32px;border-bottom:1px solid rgba(34,211,238,0.15);">
          <span style="font-size:20px;font-weight:bold;color:#fff;letter-spacing:4px;">
            $appName
          </span>
        </td>
      </tr>
      <tr>
        <td style="padding:32px;color:#ccc;font-size:14px;line-height:1.7;">
          $content
        </td>
      </tr>
      <tr>
        <td style="padding:20px 32px;border-top:1px solid rgba(34,211,238,0.15);
                   font-size:11px;color:#555;text-align:center;">
          © 2025 ORIGIN RP — Ne pas répondre à cet e-mail automatique.
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}
