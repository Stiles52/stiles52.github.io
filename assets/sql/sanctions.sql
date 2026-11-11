-- ============================================================
--  ORIGIN — Sanctions (avertissements, suspensions, bannissements)
--  À exécuter APRÈS logs.sql (phpMyAdmin ou mysql CLI)
--  users.status reste le statut effectif, recalculé par SanctionManager::refresh()
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `sanctions` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`         CHAR(36)      NOT NULL,
    `type`            ENUM('warning','suspension','ban') NOT NULL,
    `reason`          VARCHAR(500)  NOT NULL,
    `issued_by`       CHAR(36)      NULL DEFAULT NULL,
    `issued_by_name`  VARCHAR(50)   NULL DEFAULT NULL,  -- pseudo au moment de la sanction
    `expires_at`      TIMESTAMP     NULL DEFAULT NULL,  -- NULL = définitif (ou sans objet pour un avertissement)
    `revoked_at`      TIMESTAMP     NULL DEFAULT NULL,
    `revoked_by_name` VARCHAR(50)   NULL DEFAULT NULL,
    `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_active` (`user_id`, `revoked_at`, `expires_at`),
    CONSTRAINT `fk_sanction_user`   FOREIGN KEY (`user_id`)   REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sanction_issuer` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les comptes déjà suspendus / bannis reçoivent une sanction équivalente (sinon ils seraient réactivés au prochain recalcul)
INSERT INTO `sanctions` (`user_id`, `type`, `reason`)
SELECT `id`, IF(`status` = 'banned', 'ban', 'suspension'), 'Sanction antérieure au système de sanctions'
FROM `users`
WHERE `status` IN ('suspended', 'banned');
