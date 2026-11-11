<?php

class SanctionManager
{
    private Database $db;

    public const TYPES = [
        'warning'    => ['label' => 'Avertissement', 'status' => null,        'tone' => 'orange'],
        'suspension' => ['label' => 'Suspension',    'status' => 'suspended', 'tone' => 'orange'],
        'ban'        => ['label' => 'Bannissement',  'status' => 'banned',    'tone' => 'red'],
    ];

    /** Durées proposées (secondes). null = définitif. */
    public const DURATIONS = [
        '1h'        => ['label' => '1 heure',   'seconds' => 3600],
        '24h'       => ['label' => '24 heures', 'seconds' => 86400],
        '3d'        => ['label' => '3 jours',   'seconds' => 259200],
        '7d'        => ['label' => '7 jours',   'seconds' => 604800],
        '30d'       => ['label' => '30 jours',  'seconds' => 2592000],
        'permanent' => ['label' => 'Définitif', 'seconds' => null],
    ];

    /** Condition SQL « sanction en cours » (alias s). */
    private const ACTIVE = 's.revoked_at IS NULL AND (s.expires_at IS NULL OR s.expires_at > NOW())';

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Recalcule users.status à partir des sanctions en cours (levée automatique des sanctions expirées).
     * Sans $userId : seulement les comptes actuellement sanctionnés.
     */
    public function refresh(?string $userId = null): void
    {
        $this->db->query(
            "UPDATE users u SET u.status = CASE
                WHEN EXISTS (SELECT 1 FROM sanctions s WHERE s.user_id = u.id AND s.type = 'ban'        AND " . self::ACTIVE . ") THEN 'banned'
                WHEN EXISTS (SELECT 1 FROM sanctions s WHERE s.user_id = u.id AND s.type = 'suspension' AND " . self::ACTIVE . ") THEN 'suspended'
                ELSE 'active' END
             WHERE " . ($userId !== null ? 'u.id = ?' : "u.status <> 'active'"),
            $userId !== null ? [$userId] : []
        );
    }

    /** Sanction bloquante en cours (bannissement prioritaire), ou false. */
    public function current(string $userId): array|false
    {
        return $this->db->fetchOne(
            "SELECT s.* FROM sanctions s
             WHERE s.user_id = ? AND s.type IN ('ban', 'suspension') AND " . self::ACTIVE . "
             ORDER BY s.type = 'ban' DESC, s.expires_at IS NULL DESC, s.expires_at DESC
             LIMIT 1",
            [$userId]
        );
    }

    public function issue(string $userId, string $type, string $reason, string $duration, array $actor): array
    {
        $reason = trim($reason);
        $user   = $this->db->fetchOne('SELECT id, pseudo FROM users WHERE id = ?', [$userId]);

        if (!$user) {
            return $this->err('Utilisateur introuvable.');
        }
        if (!isset(self::TYPES[$type])) {
            return $this->err('Type de sanction invalide.');
        }
        if ($user['id'] === $actor['id']) {
            return $this->err('Vous ne pouvez pas vous sanctionner vous-même.');
        }
        if (mb_strlen($reason) < 3 || mb_strlen($reason) > 500) {
            return $this->err('Le motif doit contenir entre 3 et 500 caractères.');
        }

        $expiresAt = null;
        if ($type !== 'warning') {
            if (!isset(self::DURATIONS[$duration])) {
                return $this->err('Durée invalide.');
            }
            if ($type === 'suspension' && $duration === 'permanent') {
                return $this->err('Une suspension est forcément temporaire. Utilisez un bannissement pour une sanction définitive.');
            }
            $seconds   = self::DURATIONS[$duration]['seconds'];
            $expiresAt = $seconds !== null ? date('Y-m-d H:i:s', time() + $seconds) : null;
        }

        $this->db->transaction(function (Database $db) use ($user, $type, $reason, $expiresAt, $actor) {
            $db->query(
                'INSERT INTO sanctions (user_id, type, reason, issued_by, issued_by_name, expires_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$user['id'], $type, $reason, $actor['id'], $actor['pseudo'], $expiresAt]
            );
            if ($type !== 'warning') {
                $this->refresh($user['id']);
                // Déconnexion immédiate
                $db->query('DELETE FROM user_sessions WHERE user_id = ?', [$user['id']]);
            }
        });

        $label = self::TYPES[$type]['label'] . ($type !== 'warning' ? ' — ' . strtolower(self::DURATIONS[$duration]['label']) : '');
        Logger::log('admin_action', "Utilisateur \"{$user['pseudo']}\" — motif : « $reason » — sanction : ", $label, self::TYPES[$type]['tone']);

        return ['success' => true, 'message' => "Sanction appliquée à « {$user['pseudo']} » : $label."];
    }

    /** Lève toutes les suspensions / bannissements en cours. */
    public function revoke(string $userId, array $actor): array
    {
        $user = $this->db->fetchOne('SELECT id, pseudo FROM users WHERE id = ?', [$userId]);
        if (!$user) {
            return $this->err('Utilisateur introuvable.');
        }
        if (!$this->current($userId)) {
            return $this->err("« {$user['pseudo']} » n'a aucune sanction en cours.");
        }

        $this->db->transaction(function (Database $db) use ($userId, $actor) {
            $db->query(
                "UPDATE sanctions s SET s.revoked_at = NOW(), s.revoked_by_name = ?
                 WHERE s.user_id = ? AND s.type IN ('ban', 'suspension') AND " . self::ACTIVE,
                [$actor['pseudo'], $userId]
            );
            $this->refresh($userId);
        });

        Logger::log('admin_action', "Utilisateur \"{$user['pseudo']}\" — sanction levée, statut : ", 'Actif', 'green');

        return ['success' => true, 'message' => "Sanction levée : « {$user['pseudo']} » est de nouveau actif."];
    }

    /**
     * Historique complet des sanctions d'un joueur (plus récentes d'abord).
     * state : active | expired | revoked | logged (avertissement)
     */
    public function history(string $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT s.*, (" . self::ACTIVE . ") AS is_active FROM sanctions s WHERE s.user_id = ? ORDER BY s.created_at DESC, s.id DESC",
            [$userId]
        );
        foreach ($rows as &$r) {
            $r['state'] = match (true) {
                $r['type'] === 'warning'  => 'logged',
                $r['revoked_at'] !== null => 'revoked',
                (bool) $r['is_active']    => 'active',
                default                   => 'expired',
            };
        }
        return $rows;
    }

    /** Message affiché à la connexion d'un compte sanctionné. */
    public function loginMessage(string $userId): ?string
    {
        $s = $this->current($userId);
        if (!$s) {
            return null;
        }
        $what  = $s['type'] === 'ban' ? 'banni' : 'suspendu';
        $until = $s['expires_at'] ? " jusqu'au " . date('d/m/Y à H:i', strtotime($s['expires_at'])) : ' définitivement';
        return "Ce compte est $what$until. Motif : {$s['reason']}";
    }

    private function err(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
