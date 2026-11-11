<?php

/**
 * Personnages et candidatures.
 * Un personnage n'est jouable qu'une fois sa candidature acceptée ; toute modification d'un personnage
 * validé repasse en validation (l'ancienne version validée reste en vigueur jusqu'à la décision).
 */
class CharacterManager
{
    private Database $db;

    public const JOBS = [
        'Médecin'     => ['color' => '#f97316', 'badge' => 'warning'],
        'Artisan'     => ['color' => '#22d3ee', 'badge' => 'primary'],
        'Agriculteur' => ['color' => '#22c55e', 'badge' => 'success'],
        'Marchand'    => ['color' => '#eab308', 'badge' => 'warning'],
        'Soldat'      => ['color' => '#ef4444', 'badge' => 'danger'],
        'Autre'       => ['color' => '#6b7280', 'badge' => 'secondary'],
    ];

    public const LEVELS = [
        'beginner'    => ['label' => 'Débutant',   'badge' => 'secondary'],
        'confirmed'   => ['label' => 'Confirmé',   'badge' => 'primary'],
        'specialized' => ['label' => 'Spécialisé', 'badge' => 'info'],
    ];

    public const STATUSES = [
        'pending'   => ['label' => 'En attente', 'badge' => 'warning'],
        'accepted'  => ['label' => 'Acceptée',   'badge' => 'success'],
        'refused'   => ['label' => 'Refusée',    'badge' => 'danger'],
        'withdrawn' => ['label' => 'Retirée',    'badge' => 'secondary'],
    ];

    public const TYPES = [
        'creation'     => 'Création',
        'modification' => 'Modification',
    ];

    public const GENDERS  = ['Homme', 'Femme', 'Autre'];
    public const MICS     = [
        'good'    => 'Bonne qualité',
        'average' => 'Qualité correcte',
        'poor'    => 'Mauvaise qualité',
        'none'    => 'Pas de micro',
    ];
    public const VIOLENCE = [
        'physical'      => 'Accepte la violence physique',
        'psychological' => 'Accepte la violence psychologique',
        'none'          => 'Ne souhaite pas subir de scènes violentes',
    ];

    /** Champs de la fiche (ordre d'affichage) : clé => libellé. */
    public const SHEET_FIELDS = [
        'name'          => 'Prénom & Nom',
        'job'           => 'Métier',
        'age'           => 'Âge du personnage',
        'gender'        => 'Sexe',
        'origin'        => 'Origine',
        'height'        => 'Taille en jeu',  // en centimètres
        'hybrid'        => 'Hybridation',
        'hybrid_detail' => "Détail de l'hybridation",
        'appearance'    => 'Apparence physique',
        'personality'   => 'Personnalité',
        'story'         => 'Histoire & motivations',
        'description'   => 'Description courte',
    ];

    /** Taille en jeu, en centimètres. */
    public const HEIGHT_MIN = 50;
    public const HEIGHT_MAX = 300;

    public const PER_PAGE = 20;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public static function number(int|string $id): string
    {
        return '#' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    // ----------------------------------------------------------------
    //  PRÉREQUIS & LIMITES
    // ----------------------------------------------------------------

    /** État des prérequis d'une candidature pour un joueur. */
    public function prerequisites(string $userId): array
    {
        $u = $this->db->fetchOne('SELECT minecraft_pseudo, discord_id, discord_username, max_characters FROM users WHERE id = ?', [$userId]);
        return [
            'discord'          => $u['discord_id'] !== null || Discord::requirementSkipped(),
            'discord_skipped'  => $u['discord_id'] === null && Discord::requirementSkipped(),
            'discord_username' => $u['discord_username'],
            'minecraft'        => $u['minecraft_pseudo'],
            'max'              => (int) $u['max_characters'],
            'count'            => $this->countForUser($userId),
        ];
    }

    public function countForUser(string $userId): int
    {
        return (int) $this->db->fetchOne('SELECT COUNT(*) AS n FROM characters WHERE user_id = ? AND deleted_at IS NULL', [$userId])['n'];
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

    /** Personnages d'un joueur, avec le statut de leur dernière candidature. */
    public function listForUser(string $userId): array
    {
        return $this->db->fetchAll(
            'SELECT c.*, lc.id AS cand_id, lc.status AS cand_status, lc.type AS cand_type
             FROM characters c
             LEFT JOIN candidatures lc ON lc.id = (SELECT id FROM candidatures WHERE character_id = c.id ORDER BY created_at DESC, id DESC LIMIT 1)
             WHERE c.user_id = ? AND c.deleted_at IS NULL
             ORDER BY c.created_at',
            [$userId]
        );
    }

    public function find(int $id): array|false
    {
        $c = $this->db->fetchOne('SELECT * FROM characters WHERE id = ?', [$id]);
        if ($c) {
            $c['sheet'] = $c['sheet'] !== null ? json_decode($c['sheet'], true) : null;
        }
        return $c;
    }

    public function findCandidature(int $id): array|false
    {
        $c = $this->db->fetchOne('SELECT * FROM candidatures WHERE id = ?', [$id]);
        if ($c) {
            foreach (['sheet', 'player', 'previous_sheet'] as $k) {
                $c[$k] = $c[$k] !== null ? json_decode($c[$k], true) : null;
            }
        }
        return $c;
    }

    /** Candidatures d'un personnage (plus récentes d'abord). */
    public function candidaturesFor(int $characterId): array
    {
        return $this->db->fetchAll(
            'SELECT id, type, status, decision_comment, decided_by, decided_at, created_at FROM candidatures WHERE character_id = ? ORDER BY created_at DESC, id DESC',
            [$characterId]
        );
    }

    public function latestCandidature(int $characterId): array|false
    {
        $row = $this->db->fetchOne('SELECT id FROM candidatures WHERE character_id = ? ORDER BY created_at DESC, id DESC LIMIT 1', [$characterId]);
        return $row ? $this->findCandidature((int) $row['id']) : false;
    }

    public function messages(int $candidatureId): array
    {
        return $this->db->fetchAll('SELECT * FROM candidature_messages WHERE candidature_id = ? ORDER BY created_at, id', [$candidatureId]);
    }

    /** Candidatures d'un joueur (staff : autres candidatures) et compteur pour la fiche profil. */
    public function candidaturesByUser(string $userId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT ca.id, ca.type, ca.status, ca.created_at, ch.name AS character_name
             FROM candidatures ca JOIN characters ch ON ch.id = ca.character_id
             WHERE ca.user_id = ? ORDER BY ca.created_at DESC, ca.id DESC LIMIT ' . $limit,
            [$userId]
        );
    }

    public function countCandidaturesByUser(string $userId): int
    {
        return (int) $this->db->fetchOne('SELECT COUNT(*) AS n FROM candidatures WHERE user_id = ?', [$userId])['n'];
    }

    /** Vue staff : toutes les candidatures. $filters : q, status, type, sort */
    public function listAll(array $filters, int $page = 1): array
    {
        $where  = [];
        $params = [];

        $q = trim($filters['q'] ?? '');
        if ($q !== '') {
            $num      = ltrim($q, '#');
            $like     = '%' . addcslashes($q, '%_\\') . '%';
            $where[]  = '(ca.author_name LIKE ? OR u.minecraft_pseudo LIKE ? OR ch.name LIKE ?' . (ctype_digit($num) ? ' OR ca.id = ?' : '') . ')';
            array_push($params, $like, $like, $like);
            if (ctype_digit($num)) {
                $params[] = (int) $num;
            }
        }
        if (isset(self::STATUSES[$filters['status'] ?? ''])) {
            $where[]  = 'ca.status = ?';
            $params[] = $filters['status'];
        }
        if (isset(self::TYPES[$filters['type'] ?? ''])) {
            $where[]  = 'ca.type = ?';
            $params[] = $filters['type'];
        }

        $orderBy = match ($filters['sort'] ?? 'recent') {
            'oldest' => 'ca.created_at ASC, ca.id ASC',
            'alpha'  => 'ca.author_name ASC',
            default  => 'ca.created_at DESC, ca.id DESC',
        };

        $from     = 'FROM candidatures ca JOIN characters ch ON ch.id = ca.character_id LEFT JOIN users u ON u.id = ca.user_id';
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total    = (int) $this->db->fetchOne("SELECT COUNT(*) AS n $from $whereSql", $params)['n'];
        $pages    = max(1, (int) ceil($total / self::PER_PAGE));
        $page     = min(max(1, $page), $pages);
        $offset   = ($page - 1) * self::PER_PAGE;

        $rows = $this->db->fetchAll(
            "SELECT ca.id, ca.character_id, ca.type, ca.status, ca.decision_comment, ca.created_at, ca.author_name,
                    u.minecraft_pseudo, ch.name AS character_name, ch.deleted_at AS character_deleted,
                    (ca.id = (SELECT MAX(id) FROM candidatures WHERE character_id = ca.character_id)) AS is_latest
             $from $whereSql
             ORDER BY $orderBy
             LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public function stats(): array
    {
        $r = $this->db->fetchOne(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(status = 'pending'), 0)  AS pending,
                    COALESCE(SUM(status = 'accepted'), 0) AS accepted,
                    COALESCE(SUM(status = 'refused'), 0)  AS refused
             FROM candidatures"
        );
        return array_map('intval', $r);
    }

    // ----------------------------------------------------------------
    //  JOUEUR : SOUMISSION, STATUT, SUPPRESSION
    // ----------------------------------------------------------------

    /**
     * Création ($characterId null) ou modification d'un personnage.
     * Retourne ['success', 'message', 'character_id'].
     */
    public function submit(array $user, ?int $characterId, array $in): array
    {
        $pre = $this->prerequisites($user['id']);
        if (!$pre['discord']) {
            return $this->err('Vous devez lier votre compte Discord avant de déposer une candidature.');
        }

        $character = null;
        if ($characterId !== null) {
            $character = $this->find($characterId);
            if (!$character || $character['user_id'] !== $user['id'] || $character['deleted_at'] !== null) {
                return $this->err('Personnage introuvable.');
            }
        } elseif ($pre['count'] >= $pre['max']) {
            return $this->err($pre['max'] > 1
                ? "Vous avez atteint votre limite de {$pre['max']} personnages."
                : 'Vous avez déjà un personnage. Contactez le staff pour une exception.');
        }

        // Pseudo Minecraft : obligatoire, enregistré sur le compte
        $minecraft = trim((string) ($in['minecraft_pseudo'] ?? ''));
        if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/', $minecraft)) {
            return $this->err('Le pseudo Minecraft doit contenir 3 à 16 lettres, chiffres ou underscores.');
        }
        if ($this->db->fetchOne('SELECT id FROM users WHERE minecraft_pseudo = ? AND id <> ?', [$minecraft, $user['id']])) {
            return $this->err('Ce pseudo Minecraft est déjà associé à un autre compte.');
        }

        $sheet = $this->buildSheet($in);
        if (is_string($sheet)) {
            return $this->err($sheet);
        }
        $player = $this->buildPlayer($in, $minecraft, $pre['discord_username']);
        if (is_string($player)) {
            return $this->err($player);
        }

        $pending = $character ? $this->db->fetchOne(
            "SELECT id FROM candidatures WHERE character_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1",
            [$character['id']]
        ) : false;

        $sheetJson  = json_encode($sheet, JSON_UNESCAPED_UNICODE);
        $playerJson = json_encode($player, JSON_UNESCAPED_UNICODE);

        $this->db->transaction(function (Database $db) use ($user, $minecraft, $character, $pending, $sheet, $sheetJson, $playerJson, &$characterId, &$candidatureId) {
            $db->query('UPDATE users SET minecraft_pseudo = ? WHERE id = ?', [$minecraft, $user['id']]);

            if (!$character) {
                $db->query('INSERT INTO characters (user_id, name, job) VALUES (?, ?, ?)', [$user['id'], $sheet['name'], $sheet['job']]);
                $characterId = (int) $db->lastInsertId();
            } elseif ($character['sheet'] === null) {
                // Jamais validé : le nom affiché suit la version soumise
                $db->query('UPDATE characters SET name = ?, job = ? WHERE id = ?', [$sheet['name'], $sheet['job'], $character['id']]);
            }

            if ($pending) {
                // Candidature déjà en attente : la fiche soumise est remplacée, la discussion est conservée
                $db->query(
                    'UPDATE candidatures SET sheet = ?, player = ?, last_activity_at = NOW() WHERE id = ?',
                    [$sheetJson, $playerJson, $pending['id']]
                );
                $candidatureId = (int) $pending['id'];
            } else {
                $type = $character && $character['sheet'] !== null ? 'modification' : 'creation';
                $db->query(
                    'INSERT INTO candidatures (character_id, user_id, author_name, type, sheet, player) VALUES (?, ?, ?, ?, ?, ?)',
                    [$characterId, $user['id'], $user['pseudo'], $type, $sheetJson, $playerJson]
                );
                $candidatureId = (int) $db->lastInsertId();
            }
        });

        $number = self::number($candidatureId);
        if ($pending) {
            Logger::log('candidature', "Candidature $number — fiche mise à jour par le joueur");
            return ['success' => true, 'message' => "Candidature $number mise à jour.", 'character_id' => $characterId];
        }
        Logger::log('candidature', "Candidature $number déposée pour « {$sheet['name']} »");
        return ['success' => true, 'message' => "Candidature $number envoyée au staff !", 'character_id' => $characterId];
    }

    public function setActive(array $user, int $characterId, bool $active): array
    {
        $c = $this->find($characterId);
        if (!$c || $c['user_id'] !== $user['id'] || $c['deleted_at'] !== null) {
            return $this->err('Personnage introuvable.');
        }
        if ($c['sheet'] === null) {
            return $this->err("Ce personnage n'est pas encore validé.");
        }
        $this->db->query('UPDATE characters SET active = ? WHERE id = ?', [(int) $active, $characterId]);
        return ['success' => true, 'message' => "« {$c['name']} » est maintenant " . ($active ? 'actif' : 'inactif') . '.'];
    }

    /** Suppression par le joueur : le personnage disparaît de sa liste, la candidature en attente est retirée. */
    public function delete(array $user, int $characterId): array
    {
        $c = $this->find($characterId);
        if (!$c || $c['user_id'] !== $user['id'] || $c['deleted_at'] !== null) {
            return $this->err('Personnage introuvable.');
        }
        $this->db->transaction(function (Database $db) use ($characterId) {
            $db->query('UPDATE characters SET deleted_at = NOW() WHERE id = ?', [$characterId]);
            $db->query("UPDATE candidatures SET status = 'withdrawn', last_activity_at = NOW() WHERE character_id = ? AND status = 'pending'", [$characterId]);
        });
        Logger::log('candidature', "Personnage « {$c['name']} » supprimé par le joueur");
        return ['success' => true, 'message' => "Personnage « {$c['name']} » supprimé."];
    }

    // ----------------------------------------------------------------
    //  DISCUSSION
    // ----------------------------------------------------------------

    /** Accès : l'auteur, ou le staff ($staffAccess = permission view-candidatures). */
    public function canAccess(array $candidature, array $user, bool $staffAccess): bool
    {
        return $staffAccess || $candidature['user_id'] === $user['id'];
    }

    /** $asStaff : message envoyé depuis la vue staff (mention Staff + grade). */
    public function reply(int $candidatureId, array $user, bool $staffAccess, bool $asStaff, string $body): array
    {
        $cand = $this->findCandidature($candidatureId);
        if (!$cand || !$this->canAccess($cand, $user, $staffAccess)) {
            return $this->err('Candidature introuvable.');
        }
        if ($cand['status'] === 'withdrawn') {
            return $this->err('Cette candidature a été retirée.');
        }
        $body = trim($body);
        if (mb_strlen($body) < 2 || mb_strlen($body) > 4000) {
            return $this->err('Le message doit contenir entre 2 et 4000 caractères.');
        }

        $asStaff = $asStaff && $staffAccess;
        $grade   = $asStaff ? $this->db->fetchOne(
            'SELECT g.name, g.badge FROM users u JOIN user_groups g ON g.id = u.group_id WHERE u.id = ?',
            [$user['id']]
        ) : false;

        $this->db->query(
            'INSERT INTO candidature_messages (candidature_id, user_id, author_name, is_staff, author_group, author_badge, body) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$candidatureId, $user['id'], $user['pseudo'], (int) $asStaff, $grade['name'] ?? null, $grade['badge'] ?? null, $body]
        );
        $this->db->query('UPDATE candidatures SET last_activity_at = NOW() WHERE id = ?', [$candidatureId]);

        if ($asStaff && $cand['user_id'] !== $user['id']) {
            Notifier::notify(
                $cand['user_id'],
                'Nouveau message sur votre candidature ' . self::number($candidatureId),
                "Le staff vous a écrit au sujet de votre personnage « {$cand['sheet']['name']} ».",
                '/dashboard?page=personnage&id=' . (int) $cand['character_id']
            );
        }

        return ['success' => true, 'message' => 'Message envoyé.'];
    }

    // ----------------------------------------------------------------
    //  STAFF : DÉCISIONS
    // ----------------------------------------------------------------

    public function decide(int $candidatureId, string $decision, string $comment, array $actor): array
    {
        $cand = $this->findCandidature($candidatureId);
        if (!$cand) {
            return $this->err('Candidature introuvable.');
        }
        if ($cand['status'] !== 'pending') {
            return $this->err('Cette candidature a déjà été traitée.');
        }
        $comment = trim($comment);
        if ($decision === 'refused' && mb_strlen($comment) < 3) {
            return $this->err('Un commentaire est obligatoire pour refuser une candidature.');
        }
        if (mb_strlen($comment) > 500) {
            return $this->err('Le commentaire ne peut pas dépasser 500 caractères.');
        }
        if (!in_array($decision, ['accepted', 'refused'], true)) {
            return $this->err('Décision invalide.');
        }

        $character = $this->find((int) $cand['character_id']);
        if ($decision === 'accepted' && $character['deleted_at'] !== null) {
            return $this->err('Ce personnage a été supprimé par le joueur.');
        }

        $this->db->transaction(function (Database $db) use ($cand, $character, $decision, $comment, $actor) {
            if ($decision === 'accepted') {
                // La fiche soumise devient la version validée (l'ancienne est conservée pour une éventuelle annulation)
                $db->query(
                    'UPDATE candidatures SET previous_sheet = ? WHERE id = ?',
                    [$character['sheet'] !== null ? json_encode($character['sheet'], JSON_UNESCAPED_UNICODE) : null, $cand['id']]
                );
                $db->query(
                    'UPDATE characters SET sheet = ?, name = ?, job = ?, validated_at = NOW() WHERE id = ?',
                    [json_encode($cand['sheet'], JSON_UNESCAPED_UNICODE), $cand['sheet']['name'], $cand['sheet']['job'], $character['id']]
                );
            }
            $db->query(
                'UPDATE candidatures SET status = ?, decision_comment = ?, decided_by = ?, decided_at = NOW(), last_activity_at = NOW() WHERE id = ?',
                [$decision, $comment !== '' ? $comment : null, $actor['pseudo'], $cand['id']]
            );
        });

        $number = self::number($candidatureId);
        $label  = $decision === 'accepted' ? 'Acceptée' : 'Refusée';
        Notifier::notify(
            $cand['user_id'],
            "Candidature $number " . strtolower($label),
            "Votre candidature pour « {$cand['sheet']['name']} » a été " . strtolower($label) . ' par le staff.'
                . ($comment !== '' ? "\n\nCommentaire : $comment" : ''),
            '/dashboard?page=personnage&id=' . (int) $cand['character_id']
        );
        Logger::log('candidature', "Candidature $number (« {$cand['sheet']['name']} » de {$cand['author_name']}) — décision : ", $label, $decision === 'accepted' ? 'green' : 'red');

        return ['success' => true, 'message' => "Candidature $number " . strtolower($label) . '.'];
    }

    /** Remet une candidature en attente (seulement la plus récente du personnage). */
    public function cancelDecision(int $candidatureId, array $actor): array
    {
        $cand = $this->findCandidature($candidatureId);
        if (!$cand || !in_array($cand['status'], ['accepted', 'refused'], true)) {
            return $this->err("Cette candidature n'a pas de décision à annuler.");
        }
        $latest = $this->db->fetchOne('SELECT MAX(id) AS id FROM candidatures WHERE character_id = ?', [$cand['character_id']]);
        if ((int) $latest['id'] !== $candidatureId) {
            return $this->err('Une candidature plus récente existe pour ce personnage : décision non annulable.');
        }

        $this->db->transaction(function (Database $db) use ($cand) {
            if ($cand['status'] === 'accepted') {
                // Retour à la version validée précédente (aucune pour une création)
                $prev = $cand['previous_sheet'];
                $db->query(
                    'UPDATE characters SET sheet = ?, name = COALESCE(?, name), job = COALESCE(?, job), validated_at = IF(? IS NULL, NULL, validated_at) WHERE id = ?',
                    [$prev !== null ? json_encode($prev, JSON_UNESCAPED_UNICODE) : null, $prev['name'] ?? null, $prev['job'] ?? null, $prev !== null ? 1 : null, $cand['character_id']]
                );
            }
            $db->query(
                "UPDATE candidatures SET status = 'pending', previous_sheet = NULL, decision_comment = NULL, decided_by = NULL, decided_at = NULL, last_activity_at = NOW() WHERE id = ?",
                [$cand['id']]
            );
        });

        $number = self::number($candidatureId);
        Logger::log('candidature', "Candidature $number — décision annulée (remise en attente)");
        return ['success' => true, 'message' => "Décision annulée : la candidature $number est de nouveau en attente."];
    }

    public function setLevel(int $characterId, string $level): array
    {
        $c = $this->find($characterId);
        if (!$c) {
            return $this->err('Personnage introuvable.');
        }
        if (!isset(self::LEVELS[$level])) {
            return $this->err('Niveau invalide.');
        }
        $this->db->query('UPDATE characters SET level = ? WHERE id = ?', [$level, $characterId]);
        Logger::log('candidature', "Personnage « {$c['name']} » — niveau : ", self::LEVELS[$level]['label'], 'cyan');
        return ['success' => true, 'message' => "Niveau de « {$c['name']} » : " . self::LEVELS[$level]['label'] . '.'];
    }

    /** Exception à la limite d'un personnage par joueur. */
    public function setMaxCharacters(string $userId, int $max): array
    {
        if ($max < 1 || $max > 10) {
            return $this->err('La limite doit être comprise entre 1 et 10.');
        }
        $u = $this->db->fetchOne('SELECT pseudo FROM users WHERE id = ?', [$userId]);
        if (!$u) {
            return $this->err('Joueur introuvable.');
        }
        $this->db->query('UPDATE users SET max_characters = ? WHERE id = ?', [$max, $userId]);
        Logger::log('candidature', "{$u['pseudo']} — personnages autorisés : ", (string) $max, 'cyan');
        return ['success' => true, 'message' => "{$u['pseudo']} peut maintenant avoir $max personnage" . ($max > 1 ? 's' : '') . '.'];
    }

    // ----------------------------------------------------------------
    //  UTILITAIRES
    // ----------------------------------------------------------------

    /** Valeur lisible d'un champ de fiche. */
    public static function displayValue(string $key, mixed $value): string
    {
        return match ($key) {
            'hybrid' => $value ? 'Oui' : 'Non',
            'height' => is_numeric($value) ? $value . ' cm' : self::displayValue('', $value),
            default  => $value === null || $value === '' ? '—' : (string) $value,
        };
    }

    /** Fiche normalisée, ou message d'erreur. */
    private function buildSheet(array $in): array|string
    {
        $s = [];
        foreach (['name', 'origin', 'height', 'hybrid_detail', 'appearance', 'personality', 'story', 'description'] as $k) {
            $s[$k] = trim((string) ($in[$k] ?? ''));
        }
        $s['job']    = (string) ($in['job'] ?? '');
        $s['gender'] = (string) ($in['gender'] ?? '');
        $s['age']    = trim((string) ($in['char_age'] ?? ''));
        $s['hybrid'] = ($in['hybrid'] ?? '') === '1';

        $len = fn(string $v, int $min, int $max) => mb_strlen($v) >= $min && mb_strlen($v) <= $max;

        return match (true) {
            !$len($s['name'], 3, 80)                                    => 'Le nom du personnage doit contenir entre 3 et 80 caractères.',
            !isset(self::JOBS[$s['job']])                               => 'Sélectionnez le métier du personnage.',
            !ctype_digit($s['age']) || (int) $s['age'] < 1 || (int) $s['age'] > 1000 => "L'âge du personnage doit être un nombre entre 1 et 1000.",
            !in_array($s['gender'], self::GENDERS, true)                => 'Sélectionnez le sexe du personnage.',
            !$len($s['origin'], 2, 100)                                 => "Indiquez l'origine du personnage (2 à 100 caractères).",
            !ctype_digit($s['height']) || (int) $s['height'] < self::HEIGHT_MIN || (int) $s['height'] > self::HEIGHT_MAX
                => 'La taille en jeu doit être un nombre de centimètres entre ' . self::HEIGHT_MIN . ' et ' . self::HEIGHT_MAX . '.',
            $s['hybrid'] && !$len($s['hybrid_detail'], 3, 300)          => "Décrivez l'hybridation du personnage (3 à 300 caractères).",
            !$len($s['appearance'], 50, 3000)                           => "L'apparence physique doit contenir entre 50 et 3000 caractères.",
            !$len($s['personality'], 50, 3000)                          => 'La personnalité doit contenir entre 50 et 3000 caractères.',
            !$len($s['story'], 200, 8000)                               => "L'histoire & les motivations doivent contenir entre 200 et 8000 caractères.",
            mb_strlen($s['description']) > 500                          => 'La description courte ne peut pas dépasser 500 caractères.',
            default => array_merge($s, ['hybrid_detail' => $s['hybrid'] ? $s['hybrid_detail'] : '', 'age' => (int) $s['age'], 'height' => (int) $s['height']]),
        };
    }

    /** Questionnaire joueur normalisé, ou message d'erreur. */
    private function buildPlayer(array $in, string $minecraft, ?string $discord): array|string
    {
        $age       = trim((string) ($in['player_age'] ?? ''));
        $mic       = (string) ($in['mic'] ?? '');
        $epileptic = (string) ($in['epileptic'] ?? '');
        $violence  = array_values(array_intersect(array_keys(self::VIOLENCE), (array) ($in['violence'] ?? [])));

        if (!ctype_digit($age) || (int) $age < 10 || (int) $age > 99) {
            return 'Indiquez votre âge (nombre entre 10 et 99).';
        }
        if (!isset(self::MICS[$mic])) {
            return "Indiquez la qualité / l'état de votre micro.";
        }
        if (!in_array($epileptic, ['yes', 'no'], true)) {
            return "Indiquez si vous êtes épileptique (cette information n'est visible que du staff).";
        }
        if (!$violence) {
            return 'Indiquez votre rapport à la violence en jeu.';
        }
        if (in_array('none', $violence, true) && count($violence) > 1) {
            return 'Vous ne pouvez pas à la fois accepter et refuser les scènes violentes.';
        }

        return [
            'age'       => (int) $age,
            'mic'       => $mic,
            'epileptic' => $epileptic === 'yes',
            'violence'  => $violence,
            'minecraft' => $minecraft,
            'discord'   => $discord,
        ];
    }

    private function err(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
