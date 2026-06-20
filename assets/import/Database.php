<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_DATABASE']
        );

        $this->pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // vraies requêtes préparées côté MySQL
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Interdit la duplication du singleton (évite que getPdo() soit bypassé via clone)
    private function __clone(): void {}

    // Interdit la désérialisation (unserialize() ne doit pas recréer une connexion)
    public function __wakeup(): never
    {
        throw new \RuntimeException('Deserializing Database is not allowed.');
    }

    /**
     * Prépare et exécute une requête paramétrée. Retourne le PDOStatement.
     * Toujours passer les données utilisateur en $params, jamais dans $sql.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Retourne toutes les lignes d'un SELECT.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Retourne une seule ligne d'un SELECT.
     */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Retourne l'ID auto-incrémenté du dernier INSERT.
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Exécute un bloc de code dans une transaction atomique.
     * Rollback automatique si une exception est levée.
     *
     * Exemple :
     *   $db->transaction(function($db) {
     *       $db->query("INSERT INTO ...", [...]);
     *       $db->query("UPDATE ...",     [...]);
     *   });
     */
    public function transaction(callable $callback): void
    {
        $this->pdo->beginTransaction();
        try {
            $callback($this);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
