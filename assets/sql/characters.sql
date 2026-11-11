-- ============================================================
--  ORIGIN — Personnages & candidatures (1 personnage = 1 candidature validée par le staff)
--  À exécuter APRÈS tickets-staff.sql (phpMyAdmin ou mysql CLI)
--  Champs des fiches, métiers et niveaux définis dans CharacterManager
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

-- Liaison Discord (prérequis d'une candidature) + nombre de personnages autorisés (exceptions gérées par le staff)
ALTER TABLE `users`
    ADD COLUMN `discord_id`        VARCHAR(32)      NULL DEFAULT NULL AFTER `minecraft_pseudo`,
    ADD COLUMN `discord_username`  VARCHAR(64)      NULL DEFAULT NULL AFTER `discord_id`,
    ADD COLUMN `discord_linked_at` TIMESTAMP        NULL DEFAULT NULL AFTER `discord_username`,
    ADD COLUMN `max_characters`    TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `status`,
    ADD UNIQUE KEY `uq_discord` (`discord_id`);

-- Personnages : `sheet` = dernière version VALIDÉE (NULL tant que la première candidature n'est pas acceptée)
CREATE TABLE IF NOT EXISTS `characters` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      CHAR(36)     NOT NULL,
    `name`         VARCHAR(80)  NOT NULL,                      -- nom affiché (version validée, ou soumise si jamais validé)
    `job`          VARCHAR(30)  NOT NULL,
    `level`        ENUM('beginner','confirmed','specialized') NOT NULL DEFAULT 'beginner', -- géré par le staff
    `active`       TINYINT(1)   NOT NULL DEFAULT 1,             -- Actif / Inactif (choix du joueur, une fois validé)
    `sheet`        TEXT         NULL DEFAULT NULL,              -- JSON de la fiche validée
    `validated_at` TIMESTAMP    NULL DEFAULT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   TIMESTAMP    NULL DEFAULT NULL,              -- suppression par le joueur (conservé pour l'historique staff)
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`, `deleted_at`),
    CONSTRAINT `fk_char_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Candidatures : chaque soumission (création ou modification) d'une fiche
CREATE TABLE IF NOT EXISTS `candidatures` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,    -- affiché #0001
    `character_id`     INT UNSIGNED NOT NULL,
    `user_id`          CHAR(36)     NULL DEFAULT NULL,
    `author_name`      VARCHAR(50)  NOT NULL,
    `type`             ENUM('creation','modification') NOT NULL DEFAULT 'creation',
    `sheet`            TEXT         NOT NULL,                   -- JSON de la fiche soumise
    `player`           TEXT         NOT NULL,                   -- JSON du questionnaire joueur (âge, micro, avertissements…)
    `previous_sheet`   TEXT         NULL DEFAULT NULL,          -- fiche validée remplacée à l'acceptation (annulation possible)
    `status`           ENUM('pending','accepted','refused','withdrawn') NOT NULL DEFAULT 'pending',
    `decision_comment` VARCHAR(500) NULL DEFAULT NULL,
    `decided_by`       VARCHAR(50)  NULL DEFAULT NULL,
    `decided_at`       TIMESTAMP    NULL DEFAULT NULL,
    `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_activity_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_character` (`character_id`, `created_at`),
    KEY `idx_status`    (`status`, `created_at`),
    CONSTRAINT `fk_cand_character` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cand_user`      FOREIGN KEY (`user_id`)      REFERENCES `users` (`id`)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Discussion joueur ↔ staff sur une candidature
CREATE TABLE IF NOT EXISTS `candidature_messages` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `candidature_id` INT UNSIGNED NOT NULL,
    `user_id`        CHAR(36)     NULL DEFAULT NULL,
    `author_name`    VARCHAR(50)  NOT NULL,
    `is_staff`       TINYINT(1)   NOT NULL DEFAULT 0,
    `author_group`   VARCHAR(50)  NULL DEFAULT NULL,
    `author_badge`   VARCHAR(20)  NULL DEFAULT NULL,
    `body`           TEXT         NOT NULL,
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cand` (`candidature_id`, `created_at`),
    CONSTRAINT `fk_cmsg_cand` FOREIGN KEY (`candidature_id`) REFERENCES `candidatures` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cmsg_user` FOREIGN KEY (`user_id`)        REFERENCES `users` (`id`)        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
