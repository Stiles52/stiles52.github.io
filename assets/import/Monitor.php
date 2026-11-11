<?php

class Monitor
{
    private Database $db;

    private const CHECK_INTERVAL = 60;  // secondes minimum entre deux vérifications enregistrées
    private const CHECK_TIMEOUT  = 5;   // secondes avant de considérer le site hors service
    private const RETENTION_DAYS = 30;  // période de calcul de l'uptime / conservation des vérifications
    private const ONLINE_MINUTES = 5;   // un membre est « en ligne » s'il a été actif depuis N minutes
    private const METRICS_TTL    = 15;  // secondes de cache des métriques CPU / RAM

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    // ----------------------------------------------------------------
    //  VÉRIFICATION DE DISPONIBILITÉ
    // ----------------------------------------------------------------

    /**
     * Interroge la page d'accueil et enregistre le résultat.
     * Ne fait rien si la dernière vérification date de moins de CHECK_INTERVAL secondes.
     */
    public function runCheck(): ?array
    {
        $recent = $this->db->fetchOne(
            'SELECT id FROM monitoring_checks WHERE checked_at > NOW() - INTERVAL ' . self::CHECK_INTERVAL . ' SECOND LIMIT 1'
        );
        if ($recent) {
            return null;
        }

        $url = rtrim($_ENV['APP_URL'] ?? '', '/') . '/';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY         => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => self::CHECK_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::CHECK_TIMEOUT,
            CURLOPT_USERAGENT      => 'OriginMonitor/1.0',
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $ms   = (int) round(curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000);

        $result = [
            'ok'          => $code >= 200 && $code < 400,
            'status_code' => $code ?: null,
            'response_ms' => $code ? $ms : null,
        ];

        $this->db->query(
            'INSERT INTO monitoring_checks (ok, status_code, response_ms) VALUES (?, ?, ?)',
            [(int) $result['ok'], $result['status_code'], $result['response_ms']]
        );
        // Purge des anciennes vérifications
        $this->db->query('DELETE FROM monitoring_checks WHERE checked_at < NOW() - INTERVAL ' . self::RETENTION_DAYS . ' DAY');

        return $result;
    }

    // ----------------------------------------------------------------
    //  ÉTAT GLOBAL (page + API)
    // ----------------------------------------------------------------

    /** Chaque carte : ['tone' => green|orange|red|gray, 'state' => libellé, 'text' => détail] */
    public function status(): array
    {
        return [
            'updated_at' => date('d/m/Y H:i:s'),
            'site'       => $this->siteCard(),
            'response'   => $this->responseCard(),
            'online'     => $this->onlineCard(),
            'server'     => $this->serverCard(),
            'tickets'    => $this->ticketStats(),
            'activity'   => $this->recentActivity(),
        ];
    }

    private function siteCard(): array
    {
        $last = $this->db->fetchOne('SELECT ok, status_code FROM monitoring_checks ORDER BY checked_at DESC, id DESC LIMIT 1');
        $up   = $this->db->fetchOne(
            'SELECT AVG(ok) * 100 AS pct FROM monitoring_checks WHERE checked_at > NOW() - INTERVAL ' . self::RETENTION_DAYS . ' DAY'
        )['pct'];

        $uptime = $up !== null ? 'Uptime : ' . number_format((float) $up, 1, ',', ' ') . '%' : 'Uptime : aucune donnée';

        if (!$last) {
            return ['tone' => 'gray', 'state' => 'Inconnu', 'text' => $uptime];
        }
        if (!$last['ok']) {
            $why = $last['status_code'] ? "HTTP {$last['status_code']}" : 'aucune réponse';
            return ['tone' => 'red', 'state' => 'Hors service', 'text' => "$uptime — $why"];
        }
        return ['tone' => 'green', 'state' => 'Opérationnel', 'text' => $uptime];
    }

    private function responseCard(): array
    {
        $avg = $this->db->fetchOne(
            'SELECT AVG(response_ms) AS ms FROM monitoring_checks WHERE ok = 1 AND checked_at > NOW() - INTERVAL 1 DAY'
        )['ms'];

        if ($avg === null) {
            return ['tone' => 'gray', 'state' => 'Inconnu', 'text' => 'Moy. : —'];
        }
        $ms = (int) round((float) $avg);
        [$tone, $state] = match (true) {
            $ms < 500  => ['green', 'Nominal'],
            $ms < 1500 => ['orange', 'Lent'],
            default    => ['red', 'Critique'],
        };
        return ['tone' => $tone, 'state' => $state, 'text' => "Moy. : $ms ms"];
    }

    private function onlineCard(): array
    {
        $n = (int) $this->db->fetchOne(
            "SELECT COUNT(*) AS n FROM users WHERE status = 'active' AND last_seen_at > NOW() - INTERVAL " . self::ONLINE_MINUTES . ' MINUTE'
        )['n'];

        return [
            'tone'  => $n > 0 ? 'green' : 'gray',
            'state' => $n > 0 ? 'Actif' : 'Calme',
            'text'  => $n . ' membre' . ($n > 1 ? 's' : '') . ' en ligne',
        ];
    }

    private function serverCard(): array
    {
        $m = $this->serverMetrics();
        if ($m['cpu'] === null && $m['ram_total'] === null) {
            return ['tone' => 'gray', 'state' => 'Indisponible', 'text' => 'Métriques non accessibles'];
        }

        $ramPct = $m['ram_total'] ? $m['ram_used'] / $m['ram_total'] * 100 : 0;
        $load   = max($m['cpu'] ?? 0, $ramPct);
        [$tone, $state] = match (true) {
            $load < 70 => ['green', 'Normal'],
            $load < 90 => ['orange', 'Chargé'],
            default    => ['red', 'Critique'],
        };

        $cpu = $m['cpu'] !== null ? $m['cpu'] . '%' : '—';
        $ram = $m['ram_total'] !== null
            ? number_format($m['ram_used'], 1, '.', '') . ' / ' . number_format($m['ram_total'], 0, '.', '') . ' Go'
            : '—';

        return ['tone' => $tone, 'state' => $state, 'text' => "CPU : $cpu — RAM : $ram"];
    }

    /** CPU (%) et RAM (Go) de la machine, mis en cache METRICS_TTL secondes (wmic prend ~1 s sous Windows). */
    private function serverMetrics(): array
    {
        $cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'origin-monitor-metrics.json';
        $cached    = @json_decode((string) @file_get_contents($cacheFile), true);
        if (is_array($cached) && ($cached['at'] ?? 0) > time() - self::METRICS_TTL) {
            return $cached['metrics'];
        }

        $metrics = $this->readServerMetrics();
        @file_put_contents($cacheFile, json_encode(['at' => time(), 'metrics' => $metrics]), LOCK_EX);
        return $metrics;
    }

    /** null si non mesurable. */
    private function readServerMetrics(): array
    {
        $cpu = $ramUsed = $ramTotal = null;

        if (PHP_OS_FAMILY === 'Windows') {
            if (function_exists('shell_exec')) {
                if (preg_match('/LoadPercentage=(\d+)/', (string) @shell_exec('wmic cpu get loadpercentage /value'), $mm)) {
                    $cpu = (int) $mm[1];
                }
                $os = (string) @shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /value');
                if (preg_match('/FreePhysicalMemory=(\d+)/', $os, $free) && preg_match('/TotalVisibleMemorySize=(\d+)/', $os, $total)) {
                    $ramTotal = $total[1] / 1048576; // Ko → Go
                    $ramUsed  = ($total[1] - $free[1]) / 1048576;
                }
            }
        } else {
            // Linux : charge moyenne sur 1 min rapportée au nombre de cœurs
            if (function_exists('sys_getloadavg') && ($load = sys_getloadavg())) {
                $cores = max(1, (int) @shell_exec('nproc') ?: substr_count((string) @file_get_contents('/proc/cpuinfo'), 'processor'));
                $cpu   = (int) min(100, round($load[0] / $cores * 100));
            }
            $meminfo = (string) @file_get_contents('/proc/meminfo');
            if (preg_match('/MemTotal:\s+(\d+)/', $meminfo, $total) && preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $avail)) {
                $ramTotal = $total[1] / 1048576;
                $ramUsed  = ($total[1] - $avail[1]) / 1048576;
            }
        }

        return ['cpu' => $cpu, 'ram_used' => $ramUsed, 'ram_total' => $ramTotal];
    }

    private function ticketStats(): ?array
    {
        return (new TicketManager($this->db))->stats();
    }

    private function recentActivity(int $limit = 7): array
    {
        $rows = Logger::search(Logger::filtersFrom([]))['rows'];

        return array_map(function (array $log) {
            $type    = Logger::TYPES[$log['type']] ?? ['label' => $log['type'], 'badge' => 'secondary'];
            $details = Logger::plainDetails($log);
            if (in_array($log['type'], ['login', 'logout'], true)) {
                $details = 'IP : ' . Logger::maskIp($log['ip_address']) . ' — ' . $details;
            }
            return [
                'date'    => date('d/m/Y — H:i', strtotime($log['created_at'])),
                'user'    => $log['username'] ?? 'Système',
                'label'   => $type['label'],
                'badge'   => $type['badge'],
                'details' => $details,
            ];
        }, array_slice($rows, 0, $limit));
    }
}
