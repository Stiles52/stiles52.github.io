<?php

/**
 * Journal d'activité.
 *
 * Écriture : Logger::log('admin_action', 'Utilisateur "X" — statut modifié : ', 'Suspendu', 'red');
 * L'auteur de l'action est l'utilisateur connecté (défini par Auth via setActor), sauf si $user est fourni.
 */
class Logger
{
    public const TYPES = [
        'login'           => ['label' => 'Connexion',     'badge' => 'primary'],
        'logout'          => ['label' => 'Déconnexion',   'badge' => 'secondary'],
        'ticket_created'  => ['label' => 'Ticket créé',   'badge' => 'info'],
        'ticket_resolved' => ['label' => 'Ticket résolu', 'badge' => 'success'],
        'admin_action'    => ['label' => 'Action admin',  'badge' => 'danger'],
        'modification'    => ['label' => 'Modification',  'badge' => 'warning'],
        'candidature'     => ['label' => 'Candidature',   'badge' => 'blue'],
    ];

    public const PERIODS = [
        'today' => "Aujourd'hui",
        '7d'    => '7 derniers jours',
        '30d'   => '30 derniers jours',
        'all'   => 'Tout',
    ];

    public const SORTS = [
        'recent' => 'Plus récent',
        'oldest' => 'Plus ancien',
    ];

    public const TONES = ['red', 'orange', 'green', 'cyan'];

    public const PER_PAGE = 25;

    private static ?array $actor = null;

    public static function setActor(?array $user): void
    {
        self::$actor = $user;
    }

    /** Utilisateur connecté à l'origine des actions de la requête (null = système). */
    public static function actor(): ?array
    {
        return self::$actor;
    }

    // ----------------------------------------------------------------
    //  ÉCRITURE
    // ----------------------------------------------------------------

    public static function log(string $type, string $details, ?string $highlight = null, ?string $tone = null, ?array $user = null): void
    {
        $user ??= self::$actor;

        // La journalisation ne doit jamais faire échouer l'action journalisée
        try {
            Database::getInstance()->query(
                'INSERT INTO logs (user_id, username, type, details, highlight, highlight_tone, ip_address)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $user['id']     ?? null,
                    $user['pseudo'] ?? null,
                    $type,
                    mb_substr($details, 0, 500),
                    $highlight !== null ? mb_substr($highlight, 0, 100) : null,
                    in_array($tone, self::TONES, true) ? $tone : null,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                ]
            );
        } catch (\Throwable $e) {
            error_log('[Logger] ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

    /** Compteurs du jour pour les cartes de stats. */
    public static function todayStats(): array
    {
        $row = Database::getInstance()->fetchOne(
            "SELECT COUNT(*)                     AS total,
                    COALESCE(SUM(type = 'login'), 0)          AS logins,
                    COALESCE(SUM(type = 'ticket_created'), 0) AS tickets,
                    COALESCE(SUM(type = 'admin_action'), 0)   AS admin
             FROM logs
             WHERE created_at >= CURDATE()"
        );
        return array_map('intval', $row);
    }

    /**
     * Liste paginée et filtrée.
     * $filters : q (utilisateur / détails), type, period, sort
     */
    public static function search(array $filters, int $page = 1): array
    {
        [$whereSql, $params, $orderBy] = self::buildQuery($filters);
        $db = Database::getInstance();

        $total = (int) $db->fetchOne("SELECT COUNT(*) AS n FROM logs $whereSql", $params)['n'];
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min(max(1, $page), $pages);

        // LIMIT/OFFSET sont des entiers validés → interpolation sûre
        $offset = ($page - 1) * self::PER_PAGE;
        $rows   = $db->fetchAll(
            "SELECT * FROM logs $whereSql ORDER BY $orderBy LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    /** Toutes les lignes correspondant aux filtres (export), via générateur pour limiter la mémoire. */
    public static function export(array $filters): Generator
    {
        [$whereSql, $params, $orderBy] = self::buildQuery($filters);
        $stmt = Database::getInstance()->query("SELECT * FROM logs $whereSql ORDER BY $orderBy", $params);
        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }

    /** Normalise les filtres reçus en GET (valeurs inconnues → défaut). */
    public static function filtersFrom(array $input): array
    {
        return [
            'q'      => trim((string) ($input['q'] ?? '')),
            'type'   => isset(self::TYPES[$input['type'] ?? '']) ? $input['type'] : '',
            'period' => isset(self::PERIODS[$input['period'] ?? '']) ? $input['period'] : 'all',
            'sort'   => isset(self::SORTS[$input['sort'] ?? '']) ? $input['sort'] : 'recent',
        ];
    }

    /** Masque la fin de l'adresse IP (192.168.1.42 → 192.168.1.x). */
    public static function maskIp(?string $ip): string
    {
        if (!$ip) {
            return '—';
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return preg_replace('/\.\d+$/', '.x', $ip);
        }
        return implode(':', array_slice(explode(':', $ip), 0, 3)) . ':x';
    }

    /** Détails + valeur mise en avant, en texte brut (export). */
    public static function plainDetails(array $log): string
    {
        return $log['details'] . ($log['highlight'] ?? '');
    }

    private static function buildQuery(array $filters): array
    {
        $where  = [];
        $params = [];

        if ($filters['q'] !== '') {
            $like    = '%' . addcslashes($filters['q'], '%_\\') . '%';
            $where[] = '(username LIKE ? OR details LIKE ? OR highlight LIKE ?)';
            array_push($params, $like, $like, $like);
        }
        if ($filters['type'] !== '') {
            $where[]  = 'type = ?';
            $params[] = $filters['type'];
        }
        $where[] = match ($filters['period']) {
            'today' => 'created_at >= CURDATE()',
            '7d'    => 'created_at >= NOW() - INTERVAL 7 DAY',
            '30d'   => 'created_at >= NOW() - INTERVAL 30 DAY',
            default => '1 = 1',
        };

        $orderBy = $filters['sort'] === 'oldest' ? 'created_at ASC, id ASC' : 'created_at DESC, id DESC';

        return ['WHERE ' . implode(' AND ', $where), $params, $orderBy];
    }
}
