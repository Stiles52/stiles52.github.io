<?php

class TicketManager
{
    private Database $db;

    /** form : generic | bug | special (structure du formulaire de création). */
    public const CATEGORIES = [
        'haut-staff' => ['label' => 'Haut-Staff',      'badge' => 'danger',  'form' => 'generic'],
        'modo'       => ['label' => 'Modération',      'badge' => 'warning', 'form' => 'generic'],
        'recrute'    => ['label' => 'Recrutement',     'badge' => 'blue',    'form' => 'generic'],
        'bug'        => ['label' => 'Bug',             'badge' => 'warning', 'form' => 'bug'],
        'qa'         => ['label' => 'Question & Aide', 'badge' => 'info',    'form' => 'generic'],
        'ban'        => ['label' => 'Débanissement',   'badge' => 'danger',  'form' => 'generic'],
        'developper' => ['label' => 'Développement',   'badge' => 'success', 'form' => 'generic'],
        'special'    => ['label' => 'Spécialisations', 'badge' => 'warning', 'form' => 'special'],
        'scenario'   => ['label' => 'Scénarisation',   'badge' => 'primary', 'form' => 'generic'],
        'build'      => ['label' => 'Construction',    'badge' => 'info',    'form' => 'generic'],
        'animation'  => ['label' => 'Animation',       'badge' => 'primary', 'form' => 'generic'],
    ];

    public const STATUSES = [
        'new'         => ['label' => 'Nouveau',    'badge' => 'primary'],
        'in_progress' => ['label' => 'En cours',   'badge' => 'warning'],
        'waiting'     => ['label' => 'En attente', 'badge' => 'secondary'],
        'resolved'    => ['label' => 'Résolu !',   'badge' => 'success'],
        'closed'      => ['label' => 'Fermé',      'badge' => 'dark'],
    ];

    public const PRIORITIES = [
        'low'      => ['label' => 'Priorité basse',    'badge' => 'primary', 'importance' => 'Peu impactante'],
        'medium'   => ['label' => 'Priorité moyenne',  'badge' => 'warning', 'importance' => 'Moyennement impactante'],
        'high'     => ['label' => 'Priorité haute',    'badge' => 'danger',  'importance' => 'Très impactante'],
        'critical' => ['label' => 'Priorité critique', 'badge' => 'danger',  'importance' => 'Extrêmement impactante'],
    ];

    public const SORTS = [
        'recent'  => 'Réponse la plus récente',
        'oldest'  => 'Réponse la moins récente',
        'alpha'   => 'Ordre alphabetique',
        'created' => 'Ordre de création',
    ];

    /** Statut vu par le staff (calculé à partir du statut et de l'assignation, cf. staffStatus()). */
    public const STAFF_STATUSES = [
        'unassigned'  => ['label' => 'Non assigné', 'badge' => 'danger'],
        'in_progress' => ['label' => 'En cours',    'badge' => 'warning'],
        'waiting'     => ['label' => 'En attente',  'badge' => 'secondary'],
        'resolved'    => ['label' => 'Résolu',      'badge' => 'success'],
    ];

    /** Condition SQL de chaque statut staff (alias t). */
    private const STAFF_STATUS_SQL = [
        'unassigned'  => "t.status NOT IN ('resolved', 'closed') AND t.assigned_to IS NULL",
        'in_progress' => "t.status IN ('new', 'in_progress') AND t.assigned_to IS NOT NULL",
        'waiting'     => "t.status = 'waiting' AND t.assigned_to IS NOT NULL",
        'resolved'    => "t.status IN ('resolved', 'closed')",
    ];

    // Formulaire « Spécialisation »
    public const SPECIAL_JOBS   = ['Médecin', 'Artisant', 'Agriculteur'];
    public const SPECIAL_LEVELS = ['Confirmé', 'Spécialisé'];
    public const SPECIAL_SKILLS = ['Dieu', 'Maître du monde', 'Bon la vanne à était longue-'];

    // Pièces jointes (cf. textes affichés sous les champs fichier)
    public const MAX_FILES     = 10;
    public const MAX_FILE_SIZE = 80 * 1024; // 80 Ko
    private const MIME_BY_EXT  = ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'txt' => 'text/plain'];

    public const PER_PAGE      = 10;
    private const MESSAGE_MAX  = 4000;
    private const CREATE_DELAY = 60; // secondes minimum entre deux tickets d'un même joueur

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public static function number(int|string $id): string
    {
        return '#' . str_pad((string) $id, 7, '0', STR_PAD_LEFT);
    }

    public static function allowedExtensions(string $category): array
    {
        return $category === 'bug' ? ['pdf', 'png', 'jpg', 'jpeg', 'txt'] : ['pdf', 'png', 'jpg', 'jpeg'];
    }

    // ----------------------------------------------------------------
    //  DROITS
    // ----------------------------------------------------------------

    public function isStaff(array $user, GroupManager $groups): bool
    {
        return $groups->userCan($user, 'manage-tickets');
    }

    public function isParticipant(array $ticket, array $user): bool
    {
        return (bool) $this->db->fetchOne(
            'SELECT 1 FROM ticket_participants WHERE ticket_id = ? AND user_id = ?',
            [$ticket['id'], $user['id']]
        );
    }

    public function canView(array $ticket, array $user, bool $isStaff): bool
    {
        return $isStaff || $ticket['user_id'] === $user['id'] || $this->isParticipant($ticket, $user);
    }

    public function isOpen(array $ticket): bool
    {
        return !in_array($ticket['status'], ['resolved', 'closed'], true);
    }

    /** Édition possible par l'auteur tant que le staff n'a pas répondu. */
    public function canEdit(array $ticket, array $user): bool
    {
        return $ticket['user_id'] === $user['id'] && $ticket['status'] === 'new';
    }

    public function canClose(array $ticket, array $user, bool $isStaff): bool
    {
        return $this->isOpen($ticket) && ($isStaff || $ticket['user_id'] === $user['id']);
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

    public function find(int $id): array|false
    {
        $ticket = $this->db->fetchOne('SELECT * FROM tickets WHERE id = ?', [$id]);
        if ($ticket) {
            $ticket['fields'] = json_decode((string) $ticket['fields'], true) ?: [];
        }
        return $ticket;
    }

    /** Messages du ticket (ordre chronologique) avec leurs pièces jointes. */
    public function messages(int $ticketId): array
    {
        $messages = $this->db->fetchAll('SELECT * FROM ticket_messages WHERE ticket_id = ? ORDER BY created_at, id', [$ticketId]);

        $byMessage = [];
        foreach ($this->db->fetchAll('SELECT id, message_id, original_name, size FROM ticket_attachments WHERE ticket_id = ? ORDER BY id', [$ticketId]) as $a) {
            $byMessage[$a['message_id']][] = $a;
        }
        foreach ($messages as &$m) {
            $m['attachments'] = $byMessage[$m['id']] ?? [];
        }
        return $messages;
    }

    public function participants(int $ticketId): array
    {
        return array_column($this->db->fetchAll(
            'SELECT u.pseudo FROM ticket_participants p JOIN users u ON u.id = p.user_id WHERE p.ticket_id = ? ORDER BY u.pseudo',
            [$ticketId]
        ), 'pseudo');
    }

    /** Tickets dont l'utilisateur est l'auteur ou un participant. */
    public function listForUser(array $user, array $filters, int $page = 1): array
    {
        $where  = ['(t.user_id = ? OR EXISTS (SELECT 1 FROM ticket_participants p WHERE p.ticket_id = t.id AND p.user_id = ?))'];
        $params = [$user['id'], $user['id']];

        $q = trim($filters['q'] ?? '');
        if ($q !== '') {
            $num     = ltrim($q, '#');
            $where[] = '(t.title LIKE ?' . (ctype_digit($num) ? ' OR t.id = ?' : '') . ')';
            $params[] = '%' . addcslashes($q, '%_\\') . '%';
            if (ctype_digit($num)) {
                $params[] = (int) $num;
            }
        }
        if (isset(self::STATUSES[$filters['status'] ?? ''])) {
            $where[]  = 't.status = ?';
            $params[] = $filters['status'];
        }
        if (isset(self::PRIORITIES[$filters['priority'] ?? ''])) {
            $where[]  = 't.priority = ?';
            $params[] = $filters['priority'];
        }

        $orderBy = match ($filters['sort'] ?? 'recent') {
            'oldest'  => 't.last_message_at ASC, t.id ASC',
            'alpha'   => 't.title ASC',
            'created' => 't.created_at DESC, t.id DESC',
            default   => 't.last_message_at DESC, t.id DESC',
        };

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $total    = (int) $this->db->fetchOne("SELECT COUNT(*) AS n FROM tickets t $whereSql", $params)['n'];
        $pages    = max(1, (int) ceil($total / self::PER_PAGE));
        $page     = min(max(1, $page), $pages);
        $offset   = ($page - 1) * self::PER_PAGE;

        $rows = $this->db->fetchAll(
            "SELECT t.*, m.author_name AS last_author, m.body AS last_body, m.is_staff AS last_is_staff, m.author_group AS last_group
             FROM tickets t
             LEFT JOIN ticket_messages m ON m.id = (
                 SELECT id FROM ticket_messages WHERE ticket_id = t.id ORDER BY created_at DESC, id DESC LIMIT 1
             )
             $whereSql
             ORDER BY $orderBy
             LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    /**
     * Compteurs du dashboard joueur (tickets dont il est l'auteur ou participant).
     * open = non résolus ; waiting = le staff attend sa réponse ; staff = en attente d'une réponse du staff
     */
    public function userStats(string $userId): array
    {
        $r = $this->db->fetchOne(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(t.status NOT IN ('resolved', 'closed')), 0) AS open,
                    COALESCE(SUM(t.status = 'waiting'), 0)                    AS waiting,
                    COALESCE(SUM(t.status IN ('new', 'in_progress')), 0)      AS staff
             FROM tickets t
             WHERE t.user_id = ? OR EXISTS (SELECT 1 FROM ticket_participants p WHERE p.ticket_id = t.id AND p.user_id = ?)",
            [$userId, $userId]
        );
        return array_map('intval', $r);
    }

    /**
     * Compteurs pour le monitoring.
     * « En cours de traitement » = pris en charge (en cours + en attente du joueur),
     * « En attente de réponse » = nouveaux tickets sans réponse du staff.
     */
    public function stats(): array
    {
        $r = $this->db->fetchOne(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(status IN ('in_progress', 'waiting')), 0) AS in_progress,
                    COALESCE(SUM(status = 'new'), 0)                       AS waiting,
                    COALESCE(SUM(status IN ('resolved', 'closed')), 0)     AS resolved
             FROM tickets"
        );
        return array_map('intval', $r);
    }

    public static function staffStatus(array $ticket): string
    {
        return match (true) {
            in_array($ticket['status'], ['resolved', 'closed'], true) => 'resolved',
            $ticket['assigned_to'] === null                            => 'unassigned',
            $ticket['status'] === 'waiting'                            => 'waiting',
            default                                                    => 'in_progress',
        };
    }

    /** Compteurs des cartes de la gestion staff. */
    public function staffStats(): array
    {
        $cols = [];
        foreach (self::STAFF_STATUS_SQL as $key => $cond) {
            $cols[] = "COALESCE(SUM($cond), 0) AS $key";
        }
        return array_map('intval', $this->db->fetchOne('SELECT ' . implode(', ', $cols) . ' FROM tickets t'));
    }

    /** Membres pouvant traiter les tickets (permission manage-tickets). */
    public function staffMembers(): array
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.pseudo FROM users u
             JOIN group_permissions gp ON gp.group_id = u.group_id AND gp.permission = 'manage-tickets'
             WHERE u.status = 'active'
             ORDER BY u.pseudo"
        );
    }

    /**
     * Tous les tickets (vue staff).
     * $filters : q (titre, auteur, numéro), status (STAFF_STATUSES), category, assignee ('' | 'none' | id), sort
     */
    public function listAll(array $filters, int $page = 1): array
    {
        $where  = [];
        $params = [];

        $q = trim($filters['q'] ?? '');
        if ($q !== '') {
            $num      = ltrim($q, '#');
            $like     = '%' . addcslashes($q, '%_\\') . '%';
            $where[]  = '(t.title LIKE ? OR t.author_name LIKE ?' . (ctype_digit($num) ? ' OR t.id = ?' : '') . ')';
            array_push($params, $like, $like);
            if (ctype_digit($num)) {
                $params[] = (int) $num;
            }
        }
        if (isset(self::STAFF_STATUS_SQL[$filters['status'] ?? ''])) {
            $where[] = self::STAFF_STATUS_SQL[$filters['status']];
        }
        if (isset(self::CATEGORIES[$filters['category'] ?? ''])) {
            $where[]  = 't.category = ?';
            $params[] = $filters['category'];
        }
        if (($filters['assignee'] ?? '') === 'none') {
            $where[] = 't.assigned_to IS NULL';
        } elseif (($filters['assignee'] ?? '') !== '') {
            $where[]  = 't.assigned_to = ?';
            $params[] = $filters['assignee'];
        }

        $orderBy = match ($filters['sort'] ?? 'recent') {
            'oldest'  => 't.last_message_at ASC, t.id ASC',
            'alpha'   => 't.title ASC',
            'created' => 't.created_at DESC, t.id DESC',
            default   => 't.last_message_at DESC, t.id DESC',
        };

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total    = (int) $this->db->fetchOne("SELECT COUNT(*) AS n FROM tickets t $whereSql", $params)['n'];
        $pages    = max(1, (int) ceil($total / self::PER_PAGE));
        $page     = min(max(1, $page), $pages);
        $offset   = ($page - 1) * self::PER_PAGE;

        $rows = $this->db->fetchAll(
            "SELECT t.*, a.pseudo AS assignee_name
             FROM tickets t
             LEFT JOIN users a ON a.id = t.assigned_to
             $whereSql
             ORDER BY $orderBy
             LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    // ----------------------------------------------------------------
    //  CRÉATION
    // ----------------------------------------------------------------

    /**
     * @param array $input POST du formulaire
     * @param array $files $_FILES['attachments'] (multiple) ou []
     */
    public function create(array $user, string $category, array $input, array $files): array
    {
        if (!isset(self::CATEGORIES[$category])) {
            return $this->err('Type de ticket invalide.');
        }

        $recent = $this->db->fetchOne(
            'SELECT id FROM tickets WHERE user_id = ? AND created_at > NOW() - INTERVAL ' . self::CREATE_DELAY . ' SECOND LIMIT 1',
            [$user['id']]
        );
        if ($recent) {
            return $this->err('Vous venez déjà de créer un ticket. Patientez une minute avant d\'en ouvrir un nouveau.');
        }

        $built = match (self::CATEGORIES[$category]['form']) {
            'bug'     => $this->buildBug($input),
            'special' => $this->buildSpecial($input),
            default   => $this->buildGeneric($input, $user),
        };
        if (isset($built['error'])) {
            return $this->err($built['error']);
        }

        $uploads = $this->validateUploads($files, self::allowedExtensions($category));
        if (isset($uploads['error'])) {
            return $this->err($uploads['error']);
        }

        $ticketId = null;
        $stored   = [];
        try {
            $this->db->transaction(function (Database $db) use ($user, $category, $built, $uploads, &$ticketId, &$stored) {
                $db->query(
                    'INSERT INTO tickets (user_id, author_name, category, title, fields, priority) VALUES (?, ?, ?, ?, ?, ?)',
                    [$user['id'], $user['pseudo'], $category, $built['title'],
                     json_encode($built['fields'], JSON_UNESCAPED_UNICODE), $built['priority']]
                );
                $ticketId = (int) $db->lastInsertId();

                foreach ($built['participants'] as $participantId) {
                    $db->query('INSERT IGNORE INTO ticket_participants (ticket_id, user_id) VALUES (?, ?)', [$ticketId, $participantId]);
                }

                $messageId = $this->insertMessage($ticketId, $user, false, $built['body']);
                $this->storeUploads($ticketId, $messageId, $uploads, $stored);
            });
        } catch (\Throwable $e) {
            $this->deleteFiles($stored);
            error_log('[Tickets] ' . $e->getMessage());
            return $this->err("Le ticket n'a pas pu être enregistré. Réessayez.");
        }

        $number = self::number($ticketId);
        Logger::log('ticket_created', "Ticket $number ouvert — \"{$built['title']}\"");
        $this->event($ticketId, 'Ticket ouvert');
        $this->notifyDiscord($ticketId, $category, $built['title'], $user['pseudo']);

        return ['success' => true, 'message' => "Ticket $number créé !", 'id' => $ticketId];
    }

    private function buildGeneric(array $in, array $user): array
    {
        $title = trim((string) ($in['subject'] ?? ''));
        $body  = trim((string) ($in['message'] ?? ''));

        if ($e = $this->checkTitle($title) ?? $this->checkBody($body)) {
            return ['error' => $e];
        }

        // Personnes concernées : pseudos (site ou Minecraft) séparés par des virgules / espaces
        $names        = array_unique(array_filter(preg_split('/[\s,;]+/', trim((string) ($in['participants'] ?? '')))));
        $participants = [];
        $unknown      = [];
        if (count($names) > 10) {
            return ['error' => 'Vous pouvez ajouter au maximum 10 personnes à un ticket.'];
        }
        foreach ($names as $name) {
            $row = $this->db->fetchOne('SELECT id FROM users WHERE pseudo = ? OR minecraft_pseudo = ? LIMIT 1', [$name, $name]);
            if (!$row) {
                $unknown[] = $name;
            } elseif ($row['id'] !== $user['id']) {
                $participants[] = $row['id'];
            }
        }
        if ($unknown) {
            return ['error' => 'Utilisateur(s) introuvable(s) : ' . implode(', ', $unknown) . '.'];
        }

        return ['title' => $title, 'body' => $body, 'fields' => [], 'priority' => null, 'participants' => array_unique($participants)];
    }

    private function buildBug(array $in): array
    {
        $title      = trim((string) ($in['subject'] ?? ''));
        $location   = trim((string) ($in['location'] ?? ''));
        $importance = (string) ($in['importance'] ?? '');
        $body       = trim((string) ($in['message'] ?? ''));
        $video      = trim((string) ($in['video'] ?? ''));

        if ($e = $this->checkTitle($title) ?? $this->checkBody($body)) {
            return ['error' => $e];
        }
        if (mb_strlen($location) < 2 || mb_strlen($location) > 150) {
            return ['error' => "Indiquez où le bug s'est produit (2 à 150 caractères)."];
        }
        if (!isset(self::PRIORITIES[$importance])) {
            return ['error' => "Niveau d'importance invalide."];
        }
        if ($video !== '' && (!filter_var($video, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $video) || strlen($video) > 500)) {
            return ['error' => "L'URL de la vidéo est invalide."];
        }

        $fields = [
            ['label' => 'Lieu du bug', 'value' => $location],
            ['label' => 'Importance',  'value' => self::PRIORITIES[$importance]['importance']],
        ];
        if ($video !== '') {
            $fields[] = ['label' => 'Vidéo', 'value' => $video];
        }

        return ['title' => $title, 'body' => $body, 'fields' => $fields, 'priority' => $importance, 'participants' => []];
    }

    private function buildSpecial(array $in): array
    {
        $job    = (string) ($in['job'] ?? '');
        $level  = (string) ($in['level'] ?? '');
        $skill  = (string) ($in['skill'] ?? '');
        $mentor = trim((string) ($in['mentor'] ?? ''));
        $body   = trim((string) ($in['message'] ?? ''));

        if (!in_array($job, self::SPECIAL_JOBS, true)) {
            return ['error' => 'Sélectionnez le métier concerné.'];
        }
        if (!in_array($level, self::SPECIAL_LEVELS, true)) {
            return ['error' => 'Sélectionnez le niveau visé.'];
        }
        if (!in_array($skill, self::SPECIAL_SKILLS, true)) {
            return ['error' => 'Sélectionnez la spécialité concernée.'];
        }
        if (mb_strlen($mentor) > 50) {
            return ['error' => 'Le nom du mentor ne peut pas dépasser 50 caractères.'];
        }
        if ($e = $this->checkBody($body)) {
            return ['error' => $e];
        }

        return [
            'title'        => "Spécialisation : $skill ($job — $level)",
            'body'         => $body,
            'fields'       => [
                ['label' => 'Métier',     'value' => $job],
                ['label' => 'Niveau visé', 'value' => $level],
                ['label' => 'Spécialité', 'value' => $skill],
                ['label' => 'Mentor',     'value' => $mentor !== '' ? $mentor : 'Autodidacte'],
            ],
            'priority'     => null,
            'participants' => [],
        ];
    }

    // ----------------------------------------------------------------
    //  ACTIONS SUR UN TICKET
    // ----------------------------------------------------------------

    /** $fromStaffView : réponse envoyée depuis la vue staff → toujours une réponse du staff, même sur son propre ticket. */
    public function reply(int $id, array $user, bool $isStaff, string $body, array $files, bool $fromStaffView = false): array
    {
        $ticket = $this->find($id);
        if (!$ticket || !$this->canView($ticket, $user, $isStaff)) {
            return $this->err('Ticket introuvable.');
        }
        if (!$this->isOpen($ticket)) {
            return $this->err('Ce ticket est fermé : il n\'est plus possible d\'y répondre.');
        }
        $body = trim($body);
        if ($e = $this->checkBody($body)) {
            return $this->err($e);
        }
        $uploads = $this->validateUploads($files, self::allowedExtensions($ticket['category']));
        if (isset($uploads['error'])) {
            return $this->err($uploads['error']);
        }

        // Réponse du staff (hors auteur) → en attente du joueur ; réponse du joueur → de nouveau en cours
        $asStaff = $isStaff && ($fromStaffView || ($ticket['user_id'] !== $user['id'] && !$this->isParticipant($ticket, $user)));
        $status  = $asStaff ? 'waiting' : ($ticket['status'] === 'new' ? 'new' : 'in_progress');

        // Le premier membre du staff qui répond à un ticket non assigné le prend en charge
        $assignee = $asStaff && $ticket['assigned_to'] === null ? $user['id'] : $ticket['assigned_to'];

        $stored = [];
        try {
            $this->db->transaction(function (Database $db) use ($ticket, $user, $asStaff, $body, $uploads, $status, $assignee, &$stored) {
                $messageId = $this->insertMessage((int) $ticket['id'], $user, $asStaff, $body);
                $this->storeUploads((int) $ticket['id'], $messageId, $uploads, $stored);
                $db->query('UPDATE tickets SET status = ?, assigned_to = ?, last_message_at = NOW() WHERE id = ?', [$status, $assignee, $ticket['id']]);
            });
        } catch (\Throwable $e) {
            $this->deleteFiles($stored);
            error_log('[Tickets] ' . $e->getMessage());
            return $this->err("La réponse n'a pas pu être enregistrée. Réessayez.");
        }

        if ($assignee !== $ticket['assigned_to']) {
            $this->event((int) $ticket['id'], "Pris en charge par {$user['pseudo']} (première réponse)");
        }
        if ($asStaff && $ticket['user_id'] !== $user['id']) {
            $number = self::number($ticket['id']);
            Notifier::notify(
                $ticket['user_id'],
                "Nouvelle réponse sur votre ticket $number",
                "Le staff a répondu à votre ticket « {$ticket['title']} ».",
                '/dashboard?page=ticket-view&id=' . (int) $ticket['id']
            );
        }

        return ['success' => true, 'message' => 'Réponse envoyée.'];
    }

    /** L'auteur corrige le titre et la description tant que le staff n'a pas répondu. */
    public function update(int $id, array $user, string $title, string $body): array
    {
        $ticket = $this->find($id);
        if (!$ticket || !$this->canEdit($ticket, $user)) {
            return $this->err('Ce ticket ne peut plus être modifié.');
        }
        $title = trim($title);
        $body  = trim($body);

        // Le titre d'un ticket « Spécialisation » est généré à partir des choix du formulaire
        $titleEditable = self::CATEGORIES[$ticket['category']]['form'] !== 'special';
        if ($titleEditable && ($e = $this->checkTitle($title))) {
            return $this->err($e);
        }
        if ($e = $this->checkBody($body)) {
            return $this->err($e);
        }

        $this->db->transaction(function (Database $db) use ($ticket, $title, $body, $titleEditable) {
            if ($titleEditable) {
                $db->query('UPDATE tickets SET title = ? WHERE id = ?', [$title, $ticket['id']]);
            }
            $first = $db->fetchOne('SELECT id FROM ticket_messages WHERE ticket_id = ? ORDER BY created_at, id LIMIT 1', [$ticket['id']]);
            $db->query('UPDATE ticket_messages SET body = ? WHERE id = ?', [$body, $first['id']]);
        });

        $this->event((int) $ticket['id'], "Ticket modifié par l'auteur");

        return ['success' => true, 'message' => 'Ticket modifié.'];
    }

    public function close(int $id, array $user, bool $isStaff, bool $fromStaffView = false): array
    {
        $ticket = $this->find($id);
        if (!$ticket || !$this->canView($ticket, $user, $isStaff)) {
            return $this->err('Ticket introuvable.');
        }
        if (!$this->canClose($ticket, $user, $isStaff)) {
            return $this->err('Vous ne pouvez pas fermer ce ticket.');
        }

        // Fermé par le staff = résolu ; fermé par l'auteur = simplement fermé
        $byStaff = $isStaff && ($fromStaffView || $ticket['user_id'] !== $user['id']);
        $status  = $byStaff ? 'resolved' : 'closed';
        $this->db->query('UPDATE tickets SET status = ?, closed_at = NOW() WHERE id = ?', [$status, $id]);

        $number = self::number($id);
        $label  = $byStaff ? 'résolu' : 'fermé';
        Logger::log('ticket_resolved', "Ticket $number $label — \"{$ticket['title']}\"");
        $this->event($id, $byStaff ? 'Marqué comme résolu' : "Fermé par l'auteur");
        if ($byStaff && $ticket['user_id'] !== $user['id']) {
            Notifier::notify($ticket['user_id'], "Ticket $number résolu", "Votre ticket « {$ticket['title']} » a été marqué comme résolu par le staff.", '/dashboard?page=ticket-view&id=' . $id);
        }

        return ['success' => true, 'message' => "Ticket $number $label."];
    }

    // ----------------------------------------------------------------
    //  ACTIONS STAFF
    // ----------------------------------------------------------------

    /** Assigne le ticket à un membre du staff ($assigneeId), ou le désassigne (null). */
    public function assign(int $id, ?string $assigneeId): array
    {
        $ticket = $this->find($id);
        if (!$ticket) {
            return $this->err('Ticket introuvable.');
        }
        if (!$this->isOpen($ticket)) {
            return $this->err('Ce ticket est résolu : rouvrez-le avant de le réassigner.');
        }

        $name = null;
        if ($assigneeId !== null) {
            $member = array_values(array_filter($this->staffMembers(), fn($m) => $m['id'] === $assigneeId));
            if (!$member) {
                return $this->err("Ce membre ne peut pas traiter de tickets.");
            }
            $name = $member[0]['pseudo'];
        }

        $this->db->query('UPDATE tickets SET assigned_to = ? WHERE id = ?', [$assigneeId, $id]);

        $number = self::number($id);
        Logger::log('modification', "Ticket $number — assigné à : ", $name ?? 'personne', 'cyan');
        $this->event($id, $name ? "Assigné à $name" : 'Désassigné');

        return ['success' => true, 'message' => $name ? "Ticket $number assigné à $name." : "Ticket $number désassigné."];
    }

    /** Correction par le staff : titre, catégorie et priorité. */
    public function staffUpdate(int $id, string $title, string $category, string $priority): array
    {
        $ticket = $this->find($id);
        if (!$ticket) {
            return $this->err('Ticket introuvable.');
        }
        $title = trim($title);
        if ($e = $this->checkTitle($title)) {
            return $this->err($e);
        }
        if (!isset(self::CATEGORIES[$category])) {
            return $this->err('Catégorie invalide.');
        }
        if ($priority !== '' && !isset(self::PRIORITIES[$priority])) {
            return $this->err('Priorité invalide.');
        }

        $this->db->query(
            'UPDATE tickets SET title = ?, category = ?, priority = ? WHERE id = ?',
            [$title, $category, $priority !== '' ? $priority : null, $id]
        );

        $changes = [];
        if ($ticket['title'] !== $title) {
            $changes[] = 'titre';
        }
        if ($ticket['category'] !== $category) {
            $changes[] = 'catégorie (' . self::CATEGORIES[$ticket['category']]['label'] . ' → ' . self::CATEGORIES[$category]['label'] . ')';
        }
        if (($ticket['priority'] ?? '') !== $priority) {
            $changes[] = 'priorité (' . ($ticket['priority'] ? self::PRIORITIES[$ticket['priority']]['label'] : 'aucune')
                       . ' → ' . ($priority !== '' ? self::PRIORITIES[$priority]['label'] : 'aucune') . ')';
        }
        $number = self::number($id);
        if ($changes) {
            Logger::log('modification', "Ticket $number — modifié : " . implode(', ', $changes));
            $this->event($id, 'Modifié : ' . implode(', ', $changes));
        }

        return ['success' => true, 'message' => "Ticket $number modifié."];
    }

    public function reopen(int $id): array
    {
        $ticket = $this->find($id);
        if (!$ticket) {
            return $this->err('Ticket introuvable.');
        }
        if ($this->isOpen($ticket)) {
            return $this->err("Ce ticket n'est pas fermé.");
        }

        $this->db->query("UPDATE tickets SET status = 'in_progress', closed_at = NULL WHERE id = ?", [$id]);

        $number = self::number($id);
        Logger::log('modification', "Ticket $number rouvert — \"{$ticket['title']}\"");
        $this->event($id, 'Rouvert');

        return ['success' => true, 'message' => "Ticket $number rouvert."];
    }

    /** Pièce jointe téléchargeable par l'utilisateur, ou false. */
    public function attachment(int $attachmentId, array $user, bool $isStaff): array|false
    {
        $a = $this->db->fetchOne('SELECT * FROM ticket_attachments WHERE id = ?', [$attachmentId]);
        if (!$a) {
            return false;
        }
        $ticket = $this->find((int) $a['ticket_id']);
        if (!$ticket || !$this->canView($ticket, $user, $isStaff)) {
            return false;
        }
        $a['path'] = $this->storageDir((int) $a['ticket_id']) . DIRECTORY_SEPARATOR . $a['stored_name'];
        return is_file($a['path']) ? $a : false;
    }

    /** Statuts proposés dans le panneau staff. */
    public const STAFF_SETTABLE_STATUSES = [
        'new'         => 'Nouveau',
        'in_progress' => 'En cours',
        'waiting'     => 'En attente du joueur',
        'resolved'    => 'Résolu',
    ];

    /**
     * Panneau « Actions » de la vue staff : assignation, statut, priorité, catégorie en une fois.
     * Chaque changement réutilise l'action unitaire correspondante (logs + historique).
     */
    public function updateSettings(int $id, array $in, array $actor): array
    {
        $ticket = $this->find($id);
        if (!$ticket) {
            return $this->err('Ticket introuvable.');
        }

        $status   = (string) ($in['status'] ?? $ticket['status']);
        // Champ absent (désactivé sur un ticket fermé) → assignation inchangée
        $assignee = array_key_exists('assignee', $in)
            ? ((string) $in['assignee'] !== '' ? (string) $in['assignee'] : null)
            : $ticket['assigned_to'];
        $priority = (string) ($in['priority'] ?? '');
        $category = (string) ($in['category'] ?? $ticket['category']);

        if (!isset(self::STAFF_SETTABLE_STATUSES[$status]) && $status !== $ticket['status']) {
            return $this->err('Statut invalide.');
        }

        $done = [];
        $step = function (array $r) use (&$done) {
            if (!$r['success']) {
                throw new \DomainException($r['message']);
            }
            $done[] = $r['message'];
        };

        try {
            // 1. Réouverture d'abord (l'assignation est refusée sur un ticket résolu)
            if (!$this->isOpen($ticket) && $status !== $ticket['status'] && $status !== 'resolved') {
                $step($this->reopen($id));
                $ticket = $this->find($id);
            }
            // 2. Assignation
            if ($this->isOpen($ticket) && $assignee !== $ticket['assigned_to']) {
                $step($this->assign($id, $assignee));
            }
            // 3. Priorité / catégorie (titre inchangé)
            if ($priority !== ($ticket['priority'] ?? '') || $category !== $ticket['category']) {
                $step($this->staffUpdate($id, $ticket['title'], $category, $priority));
            }
            // 4. Statut
            if ($status === 'resolved' && $this->isOpen($ticket)) {
                $step($this->close($id, $actor, true));
            } elseif ($this->isOpen($ticket) && $status !== $ticket['status']) {
                $this->db->query('UPDATE tickets SET status = ? WHERE id = ?', [$status, $id]);
                $label = self::STAFF_SETTABLE_STATUSES[$status];
                Logger::log('modification', self::number($id) . ' — statut modifié : ', $label, 'cyan');
                $this->event($id, "Statut : $label");
                $done[] = "Statut : $label.";
            }
        } catch (\DomainException $e) {
            return $this->err($e->getMessage());
        }

        return ['success' => true, 'message' => $done ? 'Ticket mis à jour.' : 'Aucun changement.'];
    }

    // ---------- Notes internes

    public function notes(int $ticketId): array
    {
        return $this->db->fetchAll('SELECT * FROM ticket_notes WHERE ticket_id = ? ORDER BY created_at, id', [$ticketId]);
    }

    public function addNote(int $ticketId, array $actor, string $body): array
    {
        if (!$this->find($ticketId)) {
            return $this->err('Ticket introuvable.');
        }
        $body = trim($body);
        if (mb_strlen($body) < 2 || mb_strlen($body) > 2000) {
            return $this->err('La note doit contenir entre 2 et 2000 caractères.');
        }

        $grade = $this->db->fetchOne(
            'SELECT g.name, g.badge FROM users u JOIN user_groups g ON g.id = u.group_id WHERE u.id = ?',
            [$actor['id']]
        );
        $this->db->query(
            'INSERT INTO ticket_notes (ticket_id, user_id, author_name, author_group, author_badge, body) VALUES (?, ?, ?, ?, ?, ?)',
            [$ticketId, $actor['id'], $actor['pseudo'], $grade['name'] ?? null, $grade['badge'] ?? null, $body]
        );

        return ['success' => true, 'message' => 'Note ajoutée.'];
    }

    /** Seul l'auteur d'une note peut la supprimer. */
    public function deleteNote(int $noteId, array $actor): array
    {
        $note = $this->db->fetchOne('SELECT * FROM ticket_notes WHERE id = ?', [$noteId]);
        if (!$note || $note['user_id'] !== $actor['id']) {
            return $this->err('Vous ne pouvez supprimer que vos propres notes.');
        }
        $this->db->query('DELETE FROM ticket_notes WHERE id = ?', [$noteId]);
        return ['success' => true, 'message' => 'Note supprimée.'];
    }

    // ---------- Historique

    public function events(int $ticketId): array
    {
        return $this->db->fetchAll('SELECT * FROM ticket_events WHERE ticket_id = ? ORDER BY created_at DESC, id DESC', [$ticketId]);
    }

    // ---------- Réponses prédéfinies

    public function templates(): array
    {
        return $this->db->fetchAll('SELECT * FROM ticket_templates ORDER BY title');
    }

    public function createTemplate(string $title, string $body, array $actor): array
    {
        $title = trim($title);
        $body  = trim($body);
        if (mb_strlen($title) < 2 || mb_strlen($title) > 80) {
            return $this->err('Le nom du modèle doit contenir entre 2 et 80 caractères.');
        }
        if ($e = $this->checkBody($body)) {
            return $this->err($e);
        }
        $this->db->query('INSERT INTO ticket_templates (title, body, created_by) VALUES (?, ?, ?)', [$title, $body, $actor['pseudo']]);
        Logger::log('modification', "Réponse prédéfinie « $title » créée");
        return ['success' => true, 'message' => "Modèle « $title » créé."];
    }

    public function deleteTemplate(int $id): array
    {
        $t = $this->db->fetchOne('SELECT title FROM ticket_templates WHERE id = ?', [$id]);
        if (!$t) {
            return $this->err('Modèle introuvable.');
        }
        $this->db->query('DELETE FROM ticket_templates WHERE id = ?', [$id]);
        Logger::log('modification', "Réponse prédéfinie « {$t['title']} » supprimée");
        return ['success' => true, 'message' => "Modèle « {$t['title']} » supprimé."];
    }

    // ---------- Contexte joueur

    /** Fiche de l'auteur : compte, groupe, sanction en cours, dernières sanctions. false si le compte n'existe plus. */
    public function playerSheet(?string $userId): array|false
    {
        if ($userId === null) {
            return false;
        }
        $user = $this->db->fetchOne(
            'SELECT u.id, u.pseudo, u.minecraft_pseudo, u.email, u.status, u.created_at, u.last_login_at, u.last_seen_at,
                    g.name AS group_name, g.badge AS group_badge
             FROM users u LEFT JOIN user_groups g ON g.id = u.group_id
             WHERE u.id = ?',
            [$userId]
        );
        if (!$user) {
            return false;
        }
        $user['online']    = $user['last_seen_at'] && strtotime($user['last_seen_at']) > time() - 300;
        $user['sanctions'] = $this->db->fetchAll(
            'SELECT type, reason, issued_by_name, expires_at, revoked_at, created_at,
                    (revoked_at IS NULL AND (expires_at IS NULL OR expires_at > NOW())) AS active
             FROM sanctions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5',
            [$userId]
        );
        $user['sanction_count'] = (int) $this->db->fetchOne('SELECT COUNT(*) AS n FROM sanctions WHERE user_id = ?', [$userId])['n'];
        $user['ticket_count']   = (int) $this->db->fetchOne('SELECT COUNT(*) AS n FROM tickets WHERE user_id = ?', [$userId])['n'];
        return $user;
    }

    /** Tickets ouverts par un joueur (fiche profil) et compteurs. */
    public function byAuthor(string $userId, int $limit = 50): array
    {
        $stats = $this->db->fetchOne(
            "SELECT COUNT(*) AS total, COALESCE(SUM(status IN ('resolved', 'closed')), 0) AS resolved FROM tickets WHERE user_id = ?",
            [$userId]
        );
        $rows = $this->db->fetchAll(
            'SELECT id, title, status, assigned_to, category, created_at FROM tickets WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT ' . $limit,
            [$userId]
        );
        return ['rows' => $rows, 'total' => (int) $stats['total'], 'resolved' => (int) $stats['resolved']];
    }

    /** Autres tickets du même auteur (les plus récents d'abord). */
    public function otherTickets(array $ticket, int $limit = 8): array
    {
        if ($ticket['user_id'] === null) {
            return [];
        }
        return $this->db->fetchAll(
            'SELECT id, title, status, assigned_to, category, created_at FROM tickets
             WHERE user_id = ? AND id <> ? ORDER BY created_at DESC LIMIT ' . $limit,
            [$ticket['user_id'], $ticket['id']]
        );
    }

    // ----------------------------------------------------------------
    //  UTILITAIRES PRIVÉS
    // ----------------------------------------------------------------

    /** Ligne d'historique du ticket, attribuée à l'utilisateur connecté (ou au système). */
    private function event(int $ticketId, string $text): void
    {
        try {
            $this->db->query(
                'INSERT INTO ticket_events (ticket_id, actor_name, text) VALUES (?, ?, ?)',
                [$ticketId, Logger::actor()['pseudo'] ?? null, mb_substr($text, 0, 255)]
            );
        } catch (\Throwable $e) {
            error_log('[Tickets] ' . $e->getMessage());
        }
    }

    private function insertMessage(int $ticketId, array $user, bool $isStaff, string $body): int
    {
        // Réponse du staff : on fige le grade de l'auteur à cet instant
        $grade = $isStaff ? $this->db->fetchOne(
            'SELECT g.name, g.badge FROM users u JOIN user_groups g ON g.id = u.group_id WHERE u.id = ?',
            [$user['id']]
        ) : false;

        $this->db->query(
            'INSERT INTO ticket_messages (ticket_id, user_id, author_name, is_staff, author_group, author_badge, body) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$ticketId, $user['id'], $user['pseudo'], (int) $isStaff, $grade['name'] ?? null, $grade['badge'] ?? null, $body]
        );
        return (int) $this->db->lastInsertId();
    }

    private function checkTitle(string $title): ?string
    {
        return mb_strlen($title) < 5 || mb_strlen($title) > 150 ? 'Le titre doit contenir entre 5 et 150 caractères.' : null;
    }

    private function checkBody(string $body): ?string
    {
        return mb_strlen($body) < 10 || mb_strlen($body) > self::MESSAGE_MAX
            ? 'Le message doit contenir entre 10 et ' . self::MESSAGE_MAX . ' caractères.'
            : null;
    }

    /** Normalise et valide $_FILES['attachments'] (champ multiple). */
    private function validateUploads(array $files, array $allowedExt): array
    {
        $list = [];
        foreach ((array) ($files['name'] ?? []) as $i => $name) {
            $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                return ['error' => "« $name » dépasse la taille maximale autorisée (80 Ko)."];
            }
            if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($files['tmp_name'][$i])) {
                return ['error' => "Le fichier « $name » n'a pas pu être envoyé."];
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                return ['error' => "« $name » : format non accepté (" . strtoupper(implode(', ', array_diff($allowedExt, ['jpeg']))) . ').'];
            }
            if ($files['size'][$i] > self::MAX_FILE_SIZE) {
                return ['error' => "« $name » dépasse la taille maximale autorisée (80 Ko)."];
            }
            // Le contenu réel doit correspondre à l'extension
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($files['tmp_name'][$i]);
            if ($mime !== self::MIME_BY_EXT[$ext]) {
                return ['error' => "« $name » : le contenu du fichier ne correspond pas à son extension."];
            }

            $list[] = ['name' => mb_substr(basename($name), 0, 255), 'tmp' => $files['tmp_name'][$i], 'ext' => $ext, 'mime' => $mime, 'size' => (int) $files['size'][$i]];
        }

        if (count($list) > self::MAX_FILES) {
            return ['error' => 'Vous pouvez joindre au maximum ' . self::MAX_FILES . ' fichiers.'];
        }
        return $list;
    }

    /** Déplace les fichiers validés dans storage/tickets/<id>/ et les enregistre. $stored reçoit les chemins écrits (nettoyage en cas d'échec). */
    private function storeUploads(int $ticketId, int $messageId, array $uploads, array &$stored): void
    {
        if (!$uploads) {
            return;
        }
        $dir = $this->storageDir($ticketId);
        if (!is_dir($dir) && !mkdir($dir, 0750, true)) {
            throw new \RuntimeException("Impossible de créer $dir");
        }
        foreach ($uploads as $u) {
            $storedName = bin2hex(random_bytes(16)) . '.' . $u['ext'];
            $path       = $dir . DIRECTORY_SEPARATOR . $storedName;
            if (!move_uploaded_file($u['tmp'], $path)) {
                throw new \RuntimeException("Échec de l'écriture de {$u['name']}");
            }
            $stored[] = $path;
            $this->db->query(
                'INSERT INTO ticket_attachments (ticket_id, message_id, original_name, stored_name, mime, size) VALUES (?, ?, ?, ?, ?, ?)',
                [$ticketId, $messageId, $u['name'], $storedName, $u['mime'], $u['size']]
            );
        }
    }

    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $p) {
            @unlink($p);
        }
    }

    private function storageDir(int $ticketId): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tickets' . DIRECTORY_SEPARATOR . $ticketId;
    }

    /** Notification du staff sur Discord (webhook) — un échec n'empêche pas la création du ticket. */
    private function notifyDiscord(int $ticketId, string $category, string $title, string $author): void
    {
        $webhook = $_ENV['DISCORD_WEBHOOK'] ?? '';
        if (!preg_match('#^https://(discord|discordapp)\.com/api/webhooks/#', $webhook)) {
            return;
        }

        $colors = ['danger' => 15158332, 'warning' => 15105570, 'success' => 3066993];
        $badge  = self::CATEGORIES[$category]['badge'];
        $link   = rtrim($_ENV['APP_URL'] ?? '', '/') . '/dashboard?page=ticket-staff&id=' . $ticketId;

        $payload = json_encode([
            'username'         => 'Support ORIGIN',
            'allowed_mentions' => ['parse' => []], // aucun @everyone / @here depuis un titre de ticket
            'embeds'           => [[
                'title'       => 'Nouveau ticket ' . self::number($ticketId) . ' : ' . self::CATEGORIES[$category]['label'],
                'description' => mb_substr($title, 0, 250),
                'url'         => $link,
                'fields'      => [['name' => 'Joueur', 'value' => $author, 'inline' => true]],
                'color'       => $colors[$badge] ?? 3447003,
                'footer'      => ['text' => 'Envoyé depuis le site Origin • ' . date('d/m/Y H:i')],
            ]],
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($webhook);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 3,
        ]);
        if (curl_exec($ch) === false) {
            error_log('[Tickets] Webhook Discord : ' . curl_error($ch));
        }
    }

    private function err(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
