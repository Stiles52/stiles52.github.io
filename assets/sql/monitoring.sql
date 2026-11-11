-- ============================================================
--  ORIGIN — Monitoring
--  À exécuter APRÈS sanctions.sql (phpMyAdmin ou mysql CLI)
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

-- Dernière activité (membres « en ligne »), mise à jour par Auth::check()
ALTER TABLE `users`
    ADD COLUMN `last_seen_at` TIMESTAMP NULL DEFAULT NULL AFTER `last_login_at`,
    ADD KEY `idx_last_seen` (`last_seen_at`);

-- Résultats des vérifications de disponibilité du site (uptime / temps de réponse)
CREATE TABLE IF NOT EXISTS `monitoring_checks` (
    `id`          BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `ok`          TINYINT(1)        NOT NULL,
    `status_code` SMALLINT UNSIGNED NULL DEFAULT NULL, -- NULL = pas de réponse (timeout, DNS...)
    `response_ms` INT UNSIGNED      NULL DEFAULT NULL,
    `checked_at`  TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_checked` (`checked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nouvelle permission (mêmes groupes que view-logs)
INSERT IGNORE INTO `group_permissions` (`group_id`, `permission`)
SELECT `id`, 'view-monitoring' FROM `user_groups` WHERE `slug` IN ('administrateur', 'haut-staff', 'developpeur');
