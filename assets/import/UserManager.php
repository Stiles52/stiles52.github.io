<?php

class UserManager
{
    private Database $db;

    public const STATUSES = [
        'active'    => ['label' => 'Actif',    'badge' => 'success'],
        'suspended' => ['label' => 'Suspendu', 'badge' => 'warning'],
        'banned'    => ['label' => 'Banni',    'badge' => 'danger'],
    ];

    public const SORTS = [
        'recent'     => "Date d'inscription (récent)",
        'alpha'      => 'Alphabétique',
        'last_login' => 'Dernière connexion',
    ];

    public const PER_PAGE = 20;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

    public function groups(): array
    {
        return $this->db->fetchAll('SELECT * FROM user_groups ORDER BY position, name');
    }

    public function defaultGroupId(): ?int
    {
        $row = $this->db->fetchOne('SELECT id FROM user_groups WHERE is_default = 1 LIMIT 1');
        return $row ? (int) $row['id'] : null;
    }

    public function find(string $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT id, pseudo, minecraft_pseudo, email, status, group_id FROM users WHERE id = ?',
            [$id]
        );
    }

    /** Profil complet (sans le mot de passe), avec le groupe. */
    public function profile(string $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT u.id, u.pseudo, u.minecraft_pseudo, u.email, u.status, u.group_id, u.created_at,
                    u.last_login_at, u.last_seen_at, g.name AS group_name, g.badge AS group_badge
             FROM users u LEFT JOIN user_groups g ON g.id = u.group_id
             WHERE u.id = ?',
            [$id]
        );
    }

    /**
     * Liste paginée et filtrée.
     * $filters : q (pseudo site/Minecraft/e-mail), group (slug), status, sort
     * Retourne ['rows' => [...], 'total' => int, 'page' => int, 'pages' => int]
     */
    public function search(array $filters, int $page = 1): array
    {
        // Les suspensions expirées sont levées avant l'affichage
        (new SanctionManager($this->db))->refresh();

        $where  = [];
        $params = [];

        $q = trim($filters['q'] ?? '');
        if ($q !== '') {
            $like     = '%' . addcslashes($q, '%_\\') . '%';
            $where[]  = '(u.pseudo LIKE ? OR u.minecraft_pseudo LIKE ? OR u.email LIKE ?)';
            array_push($params, $like, $like, $like);
        }

        if (!empty($filters['group'])) {
            $where[]  = 'g.slug = ?';
            $params[] = $filters['group'];
        }

        if (!empty($filters['status']) && isset(self::STATUSES[$filters['status']])) {
            $where[]  = 'u.status = ?';
            $params[] = $filters['status'];
        }

        $orderBy = match ($filters['sort'] ?? 'recent') {
            'alpha'      => 'u.pseudo ASC',
            'last_login' => 'u.last_login_at IS NULL, u.last_login_at DESC',
            default      => 'u.created_at DESC',
        };

        $from     = 'FROM users u LEFT JOIN user_groups g ON g.id = u.group_id';
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = (int) $this->db->fetchOne("SELECT COUNT(*) AS n $from $whereSql", $params)['n'];
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min(max(1, $page), $pages);

        // LIMIT/OFFSET sont des entiers validés → interpolation sûre
        $offset = ($page - 1) * self::PER_PAGE;
        $rows   = $this->db->fetchAll(
            "SELECT u.id, u.pseudo, u.minecraft_pseudo, u.email, u.status, u.group_id,
                    u.created_at, u.last_login_at,
                    g.name AS group_name, g.badge AS group_badge,
                    cs.reason AS sanction_reason, cs.expires_at AS sanction_until
             $from
             LEFT JOIN sanctions cs ON cs.id = (
                 SELECT s.id FROM sanctions s
                 WHERE s.user_id = u.id AND s.type IN ('ban', 'suspension')
                   AND s.revoked_at IS NULL AND (s.expires_at IS NULL OR s.expires_at > NOW())
                 ORDER BY s.type = 'ban' DESC, s.expires_at IS NULL DESC, s.expires_at DESC
                 LIMIT 1
             )
             $whereSql
             ORDER BY $orderBy
             LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    // ----------------------------------------------------------------
    //  ÉCRITURE
    // ----------------------------------------------------------------

    public function create(array $data): array
    {
        $pseudo    = trim($data['pseudo'] ?? '');
        $email     = strtolower(trim($data['email'] ?? ''));
        $minecraft = trim($data['minecraft_pseudo'] ?? '');
        $password  = $data['password'] ?? '';
        $groupId   = $this->resolveGroupId($data['group_id'] ?? null);

        if ($error = $this->validate($pseudo, $email, $minecraft, null)) {
            return $this->err($error);
        }
        if (strlen($password) < 8) {
            return $this->err('Le mot de passe doit contenir au moins 8 caractères.');
        }
        if ($groupId === false) {
            return $this->err('Groupe invalide.');
        }

        $this->db->query(
            'INSERT INTO users (id, pseudo, minecraft_pseudo, email, password, group_id, email_verified_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [
                $this->generateUuid(),
                $pseudo,
                $minecraft !== '' ? $minecraft : null,
                $email,
                password_hash($password, PASSWORD_BCRYPT, ['cost' => Auth::BCRYPT_COST]),
                $groupId,
            ]
        );

        Logger::log('admin_action', "Utilisateur \"$pseudo\" créé — groupe : ", $this->groupName($groupId), 'cyan');

        return ['success' => true, 'message' => "Utilisateur « $pseudo » créé."];
    }

    public function update(string $id, array $data): array
    {
        $old = $this->find($id);
        if (!$old) {
            return $this->err('Utilisateur introuvable.');
        }

        $pseudo    = trim($data['pseudo'] ?? '');
        $email     = strtolower(trim($data['email'] ?? ''));
        $minecraft = trim($data['minecraft_pseudo'] ?? '');
        $groupId   = $this->resolveGroupId($data['group_id'] ?? null);

        if ($error = $this->validate($pseudo, $email, $minecraft, $id)) {
            return $this->err($error);
        }
        if ($groupId === false) {
            return $this->err('Groupe invalide.');
        }

        // Le mot de passe n'est volontairement pas modifiable ici (réinitialisation par l'utilisateur lui-même)
        $this->db->query(
            'UPDATE users SET pseudo = ?, minecraft_pseudo = ?, email = ?, group_id = ? WHERE id = ?',
            [$pseudo, $minecraft !== '' ? $minecraft : null, $email, $groupId, $id]
        );

        $changes = [];
        if ($old['pseudo'] !== $pseudo) {
            $changes[] = "pseudo ({$old['pseudo']} → $pseudo)";
        }
        if (($old['minecraft_pseudo'] ?? '') !== $minecraft) {
            $changes[] = 'pseudo Minecraft (' . ($old['minecraft_pseudo'] ?: '—') . ' → ' . ($minecraft ?: '—') . ')';
        }
        if ($old['email'] !== $email) {
            $changes[] = 'e-mail';
        }
        if ((int) $old['group_id'] !== (int) $groupId) {
            $changes[] = 'groupe (' . $this->groupName($old['group_id']) . ' → ' . $this->groupName($groupId) . ')';
        }
        if ($changes) {
            Logger::log('modification', "Utilisateur \"$pseudo\" — modifié : " . implode(', ', $changes));
        }

        return ['success' => true, 'message' => "Utilisateur « $pseudo » mis à jour."];
    }

    /**
     * Paramètres modifiés par le joueur lui-même (page Paramètres).
     * Changer d'e-mail ou de mot de passe exige le mot de passe actuel.
     * Retourne ['success', 'message', 'password_changed' => bool].
     */
    public function updateOwnSettings(string $id, array $in): array
    {
        $user = $this->db->fetchOne('SELECT pseudo, email, minecraft_pseudo, password, email_notifications FROM users WHERE id = ?', [$id]);
        if (!$user) {
            return $this->err('Compte introuvable.');
        }

        $pseudo        = trim((string) ($in['pseudo'] ?? ''));
        $email         = strtolower(trim((string) ($in['email'] ?? '')));
        $minecraft     = trim((string) ($in['minecraft_pseudo'] ?? ''));
        $notifications = ($in['email_notifications'] ?? '1') === '1';
        $current       = (string) ($in['current_password'] ?? '');
        $newPassword   = (string) ($in['new_password'] ?? '');
        $confirm       = (string) ($in['new_password_confirm'] ?? '');

        if ($error = $this->validate($pseudo, $email, $minecraft, $id)) {
            return $this->err($error);
        }

        $emailChanged    = $email !== $user['email'];
        $passwordChanged = $newPassword !== '' || $confirm !== '';
        if (($emailChanged || $passwordChanged) && !password_verify($current, $user['password'])) {
            return $this->err($emailChanged && !$passwordChanged
                ? "Saisissez votre mot de passe actuel pour changer d'adresse e-mail."
                : 'Le mot de passe actuel est incorrect.');
        }
        if ($passwordChanged) {
            if ($newPassword !== $confirm) {
                return $this->err('Les nouveaux mots de passe ne correspondent pas.');
            }
            if (strlen($newPassword) < 8) {
                return $this->err('Le nouveau mot de passe doit contenir au moins 8 caractères.');
            }
        }

        $this->db->query(
            'UPDATE users SET pseudo = ?, email = ?, minecraft_pseudo = ?, email_notifications = ? WHERE id = ?',
            [$pseudo, $email, $minecraft !== '' ? $minecraft : null, (int) $notifications, $id]
        );
        if ($passwordChanged) {
            $this->db->query(
                'UPDATE users SET password = ? WHERE id = ?',
                [password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => Auth::BCRYPT_COST]), $id]
            );
        }

        $changes = [];
        if ($user['pseudo'] !== $pseudo) {
            $changes[] = "pseudo ({$user['pseudo']} → $pseudo)";
        }
        if (($user['minecraft_pseudo'] ?? '') !== $minecraft) {
            $changes[] = 'pseudo Minecraft (' . ($user['minecraft_pseudo'] ?: '—') . ' → ' . ($minecraft ?: '—') . ')';
        }
        if ($emailChanged) {
            $changes[] = 'e-mail';
        }
        if ((bool) $user['email_notifications'] !== $notifications) {
            $changes[] = 'notifications ' . ($notifications ? 'activées' : 'désactivées');
        }
        if ($passwordChanged) {
            $changes[] = 'mot de passe';
        }
        if ($changes) {
            Logger::log('modification', 'Paramètres du compte modifiés : ' . implode(', ', $changes));
        }

        return [
            'success'          => true,
            'message'          => $changes ? 'Modifications enregistrées.' : 'Aucune modification.',
            'password_changed' => $passwordChanged,
        ];
    }

    // ----------------------------------------------------------------
    //  UTILITAIRES PRIVÉS
    // ----------------------------------------------------------------

    /** Retourne un message d'erreur, ou null si tout est valide. */
    private function validate(string $pseudo, string $email, string $minecraft, ?string $exceptId): ?string
    {
        if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
            return 'Le pseudonyme doit contenir entre 3 et 50 caractères.';
        }
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $pseudo)) {
            return 'Le pseudonyme ne peut contenir que des lettres, chiffres, tirets et underscores.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Adresse e-mail invalide.';
        }
        if ($minecraft !== '' && !preg_match('/^[a-zA-Z0-9_]{3,16}$/', $minecraft)) {
            return 'Le pseudo Minecraft doit contenir 3 à 16 lettres, chiffres ou underscores.';
        }

        $exceptId ??= '';
        if ($this->db->fetchOne('SELECT id FROM users WHERE email = ? AND id <> ?', [$email, $exceptId])) {
            return 'Cette adresse e-mail est déjà utilisée.';
        }
        if ($this->db->fetchOne('SELECT id FROM users WHERE pseudo = ? AND id <> ?', [$pseudo, $exceptId])) {
            return 'Ce pseudonyme est déjà pris.';
        }
        if ($minecraft !== '' && $this->db->fetchOne('SELECT id FROM users WHERE minecraft_pseudo = ? AND id <> ?', [$minecraft, $exceptId])) {
            return 'Ce pseudo Minecraft est déjà associé à un compte.';
        }
        return null;
    }

    private function groupName(int|string|null $groupId): string
    {
        $row = $groupId ? $this->db->fetchOne('SELECT name FROM user_groups WHERE id = ?', [(int) $groupId]) : false;
        return $row ? $row['name'] : 'Aucun';
    }

    /** null si vide, false si inexistant, sinon l'ID du groupe. */
    private function resolveGroupId(mixed $groupId): int|false|null
    {
        if ($groupId === null || $groupId === '') {
            return $this->defaultGroupId();
        }
        if (!ctype_digit((string) $groupId)) {
            return false;
        }
        $row = $this->db->fetchOne('SELECT id FROM user_groups WHERE id = ?', [(int) $groupId]);
        return $row ? (int) $row['id'] : false;
    }

    private function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function err(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
