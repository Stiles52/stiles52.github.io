<?php

class GroupManager
{
    private Database $db;
    private array    $permissionCache = [];

    /** Permissions disponibles, regroupées par section (ordre d'affichage de la matrice). */
    public const PERMISSIONS = [
        'Tickets'        => ['create-ticket', 'view-own-tickets', 'manage-tickets'],
        'Candidatures'   => ['view-candidatures', 'manage-candidatures'],
        'Utilisateurs'   => ['view-users', 'manage-users'],
        'Administration' => ['manage-groups', 'manage-permissions', 'view-logs', 'view-monitoring'],
    ];

    /** Couleurs proposées dans la modale → variante de badge + classes Tailwind de l'icône (carte / en-tête de matrice). */
    public const PALETTE = [
        '#ef4444' => ['badge' => 'danger',    'card' => 'text-red-500',    'head' => 'text-red-400'],
        '#a855f7' => ['badge' => 'warning',   'card' => 'text-purple-500', 'head' => 'text-purple-400'],
        '#f97316' => ['badge' => 'warning',   'card' => 'text-orange-500', 'head' => 'text-orange-400'],
        '#22d3ee' => ['badge' => 'primary',   'card' => 'text-cyan-500',   'head' => 'text-cyan-400'],
        '#22c55e' => ['badge' => 'success',   'card' => 'text-green-500',  'head' => 'text-green-400'],
        '#ec4899' => ['badge' => 'primary',   'card' => 'text-pink-500',   'head' => 'text-pink-400'],
        '#eab308' => ['badge' => 'warning',   'card' => 'text-yellow-500', 'head' => 'text-yellow-400'],
        '#4b5563' => ['badge' => 'secondary', 'card' => 'text-gray-400',   'head' => 'text-gray-400'],
    ];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public static function allPermissions(): array
    {
        return array_merge(...array_values(self::PERMISSIONS));
    }

    // ----------------------------------------------------------------
    //  CONTRÔLE D'ACCÈS
    // ----------------------------------------------------------------

    public function userPermissions(?array $user): array
    {
        if (!$user) {
            return [];
        }
        return $this->permissionCache[$user['id']] ??= array_column($this->db->fetchAll(
            "SELECT gp.permission
             FROM users u
             JOIN group_permissions gp ON gp.group_id = u.group_id
             WHERE u.id = ? AND u.status = 'active'",
            [$user['id']]
        ), 'permission');
    }

    public function userCan(?array $user, string ...$permissions): bool
    {
        // Vrai si l'utilisateur possède AU MOINS une des permissions demandées
        return (bool) array_intersect($permissions, $this->userPermissions($user));
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

    /** Groupes triés, avec nombre de membres et liste de permissions. */
    public function all(): array
    {
        $groups = $this->db->fetchAll(
            'SELECT g.*, COUNT(u.id) AS members
             FROM user_groups g
             LEFT JOIN users u ON u.group_id = g.id
             GROUP BY g.id
             ORDER BY g.position, g.name'
        );

        $perms = [];
        foreach ($this->db->fetchAll('SELECT group_id, permission FROM group_permissions') as $row) {
            $perms[$row['group_id']][] = $row['permission'];
        }

        $order = array_flip(self::allPermissions());
        foreach ($groups as &$g) {
            $g['permissions'] = $perms[$g['id']] ?? [];
            usort($g['permissions'], fn($a, $b) => ($order[$a] ?? 99) <=> ($order[$b] ?? 99));
            $g['palette'] = self::PALETTE[$g['color']] ?? self::PALETTE['#4b5563'];
        }
        return $groups;
    }

    // ----------------------------------------------------------------
    //  ÉCRITURE
    // ----------------------------------------------------------------

    public function create(array $data): array
    {
        $name  = trim($data['name'] ?? '');
        $color = $data['color'] ?? '';
        $perms = $this->cleanPermissions($data['permissions'] ?? []);

        if ($error = $this->validate($name, $color, null)) {
            return $this->err($error);
        }

        // Nouveau groupe : placé après les groupes existants, mais avant le groupe par défaut
        $position = (int) $this->db->fetchOne('SELECT COALESCE(MAX(position), 0) + 10 AS p FROM user_groups WHERE is_default = 0')['p'];

        $this->db->transaction(function (Database $db) use ($name, $color, $perms, $position) {
            $db->query(
                'INSERT INTO user_groups (slug, name, badge, color, icon, position) VALUES (?, ?, ?, ?, ?, ?)',
                [$this->uniqueSlug($name), $name, self::PALETTE[$color]['badge'], $color, 'users', $position]
            );
            $this->replacePermissions((int) $db->lastInsertId(), $perms);
        });

        Logger::log('modification', "Groupe \"$name\" créé — " . count($perms) . ' permission(s)');

        return ['success' => true, 'message' => "Groupe « $name » créé."];
    }

    public function update(int $id, array $data, array $currentUser): array
    {
        $group = $this->db->fetchOne('SELECT * FROM user_groups WHERE id = ?', [$id]);
        if (!$group) {
            return $this->err('Groupe introuvable.');
        }

        $name  = trim($data['name'] ?? '');
        $color = $data['color'] ?? '';
        $perms = $this->cleanPermissions($data['permissions'] ?? []);

        if ($error = $this->validate($name, $color, $id)) {
            return $this->err($error);
        }
        if ($this->userGroupId($currentUser) === $id && !in_array('manage-groups', $perms, true)) {
            return $this->err('Vous ne pouvez pas retirer « manage-groups » de votre propre groupe.');
        }

        $oldPerms = $this->groupPermissions($id);

        $this->db->transaction(function (Database $db) use ($id, $name, $color, $perms) {
            $db->query(
                'UPDATE user_groups SET name = ?, badge = ?, color = ? WHERE id = ?',
                [$name, self::PALETTE[$color]['badge'], $color, $id]
            );
            $this->replacePermissions($id, $perms);
        });

        if ($group['name'] !== $name) {
            Logger::log('modification', "Groupe \"{$group['name']}\" renommé en ", $name, 'cyan');
        }
        if ($group['color'] !== $color) {
            Logger::log('modification', "Groupe \"$name\" — couleur du badge modifiée : ", $color, 'cyan');
        }
        $this->logPermissionDiff($name, $oldPerms, $perms);

        return ['success' => true, 'message' => "Groupe « $name » mis à jour."];
    }

    public function delete(int $id, array $currentUser): array
    {
        $group = $this->db->fetchOne('SELECT * FROM user_groups WHERE id = ?', [$id]);
        if (!$group) {
            return $this->err('Groupe introuvable.');
        }
        if ($group['is_default']) {
            return $this->err('Le groupe par défaut ne peut pas être supprimé.');
        }
        if ($this->userGroupId($currentUser) === $id) {
            return $this->err('Vous ne pouvez pas supprimer votre propre groupe.');
        }

        $members = (int) $this->db->fetchOne('SELECT COUNT(*) AS n FROM users WHERE group_id = ?', [$id])['n'];

        $this->db->transaction(function (Database $db) use ($id) {
            // Les membres rejoignent le groupe par défaut
            $db->query(
                'UPDATE users SET group_id = (SELECT id FROM (SELECT id FROM user_groups WHERE is_default = 1 LIMIT 1) d) WHERE group_id = ?',
                [$id]
            );
            $db->query('DELETE FROM user_groups WHERE id = ?', [$id]); // group_permissions supprimées en cascade
        });

        Logger::log('admin_action', "Groupe \"{$group['name']}\" supprimé — membres déplacés vers le groupe par défaut : ", (string) $members, 'red');

        return ['success' => true, 'message' => "Groupe « {$group['name']} » supprimé. Ses membres ont rejoint le groupe par défaut."];
    }

    /**
     * Enregistre toute la matrice : [groupId => [permission, ...]].
     * Les groupes absents de la matrice ne sont pas modifiés.
     */
    public function saveMatrix(array $matrix, array $currentUser): array
    {
        $ids = array_column($this->db->fetchAll('SELECT id FROM user_groups'), 'id');

        $clean = [];
        foreach ($matrix as $groupId => $perms) {
            if (!ctype_digit((string) $groupId) || !in_array((int) $groupId, array_map('intval', $ids), true) || !is_array($perms)) {
                return $this->err('Matrice de permissions invalide.');
            }
            $clean[(int) $groupId] = $this->cleanPermissions($perms);
        }

        $ownGroup = $this->userGroupId($currentUser);
        if ($ownGroup !== null && isset($clean[$ownGroup]) && !in_array('manage-permissions', $clean[$ownGroup], true)) {
            return $this->err('Vous ne pouvez pas retirer « manage-permissions » de votre propre groupe.');
        }

        $before = [];
        foreach (array_keys($clean) as $groupId) {
            $before[$groupId] = $this->groupPermissions($groupId);
        }

        $this->db->transaction(function () use ($clean) {
            foreach ($clean as $groupId => $perms) {
                $this->replacePermissions($groupId, $perms);
            }
        });
        $this->permissionCache = [];

        $names = array_column($this->db->fetchAll('SELECT id, name FROM user_groups'), 'name', 'id');
        foreach ($clean as $groupId => $perms) {
            $this->logPermissionDiff($names[$groupId], $before[$groupId], $perms);
        }

        return ['success' => true, 'message' => 'Permissions sauvegardées avec succès'];
    }

    // ----------------------------------------------------------------
    //  UTILITAIRES PRIVÉS
    // ----------------------------------------------------------------

    private function validate(string $name, string $color, ?int $exceptId): ?string
    {
        if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
            return 'Le nom du groupe doit contenir entre 2 et 50 caractères.';
        }
        if (!isset(self::PALETTE[$color])) {
            return 'Couleur de badge invalide.';
        }
        if ($this->db->fetchOne('SELECT id FROM user_groups WHERE name = ? AND id <> ?', [$name, $exceptId ?? 0])) {
            return 'Un groupe porte déjà ce nom.';
        }
        return null;
    }

    private function groupPermissions(int $groupId): array
    {
        return array_column($this->db->fetchAll('SELECT permission FROM group_permissions WHERE group_id = ?', [$groupId]), 'permission');
    }

    /** Une ligne de log par permission ajoutée / retirée. */
    private function logPermissionDiff(string $groupName, array $before, array $after): void
    {
        foreach (array_diff($after, $before) as $perm) {
            Logger::log('modification', "Groupe \"$groupName\" — permission \"$perm\" ", 'ajoutée', 'green');
        }
        foreach (array_diff($before, $after) as $perm) {
            Logger::log('modification', "Groupe \"$groupName\" — permission \"$perm\" ", 'retirée', 'red');
        }
    }

    /** Ne garde que les permissions connues, sans doublon. */
    private function cleanPermissions(mixed $perms): array
    {
        return is_array($perms) ? array_values(array_intersect(self::allPermissions(), $perms)) : [];
    }

    private function replacePermissions(int $groupId, array $perms): void
    {
        $this->db->query('DELETE FROM group_permissions WHERE group_id = ?', [$groupId]);
        foreach ($perms as $perm) {
            $this->db->query('INSERT INTO group_permissions (group_id, permission) VALUES (?, ?)', [$groupId, $perm]);
        }
    }

    private function userGroupId(array $user): ?int
    {
        $row = $this->db->fetchOne('SELECT group_id FROM users WHERE id = ?', [$user['id']]);
        return $row && $row['group_id'] !== null ? (int) $row['group_id'] : null;
    }

    private function uniqueSlug(string $name): string
    {
        // Translittération manuelle : iconv//TRANSLIT dépend de la locale et produit des « ' » parasites sous Windows
        $ascii = strtr(mb_strtolower($name), [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ç' => 'c', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ú' => 'u', 'ÿ' => 'y', 'ñ' => 'n', 'œ' => 'oe', 'æ' => 'ae',
        ]);
        $base = trim(preg_replace('/[^a-z0-9]+/', '-', $ascii), '-') ?: 'groupe';
        $slug = $base;
        for ($i = 2; $this->db->fetchOne('SELECT id FROM user_groups WHERE slug = ?', [$slug]); $i++) {
            $slug = "$base-$i";
        }
        return $slug;
    }

    private function err(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
