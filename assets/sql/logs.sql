-- ============================================================
--  ORIGIN — Journal d'activité
--  À exécuter APRÈS permissions.sql (phpMyAdmin ou mysql CLI)
--  Les types disponibles sont définis dans Logger::TYPES
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `logs` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`        CHAR(36)        NULL DEFAULT NULL,
    `username`       VARCHAR(50)     NULL DEFAULT NULL, -- pseudo au moment de l'action (conservé si le compte change / disparaît)
    `type`           VARCHAR(30)     NOT NULL,
    `details`        VARCHAR(500)    NOT NULL,
    `highlight`      VARCHAR(100)    NULL DEFAULT NULL, -- valeur mise en avant à la fin des détails (ex : nouveau statut)
    `highlight_tone` VARCHAR(10)     NULL DEFAULT NULL, -- red | orange | green | cyan
    `ip_address`     VARCHAR(45)     NULL DEFAULT NULL,
    `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_created` (`created_at`),
    KEY `idx_type`    (`type`, `created_at`),
    KEY `idx_user`    (`user_id`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
