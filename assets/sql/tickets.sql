-- ============================================================
--  ORIGIN — Tickets de support
--  À exécuter APRÈS monitoring.sql (phpMyAdmin ou mysql CLI)
--  Catégories, statuts et priorités définis dans TicketManager
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `tickets` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,  -- affiché #0000001
    `user_id`         CHAR(36)      NULL DEFAULT NULL,
    `author_name`     VARCHAR(50)   NOT NULL,                 -- pseudo à la création (conservé si le compte disparaît)
    `category`        VARCHAR(20)   NOT NULL,
    `title`           VARCHAR(150)  NOT NULL,
    `fields`          TEXT          NULL DEFAULT NULL,        -- JSON : réponses spécifiques à la catégorie [{label, value}]
    `priority`        ENUM('low','medium','high','critical') NULL DEFAULT NULL,
    `status`          ENUM('new','in_progress','waiting','resolved','closed') NOT NULL DEFAULT 'new',
    `assigned_to`     CHAR(36)      NULL DEFAULT NULL,        -- membre du staff en charge (gestion staff)
    `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_message_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `closed_at`       TIMESTAMP     NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user`     (`user_id`),
    KEY `idx_status`   (`status`),
    KEY `idx_last_msg` (`last_message_at`),
    CONSTRAINT `fk_ticket_user`     FOREIGN KEY (`user_id`)     REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_ticket_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ticket_messages` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id`   INT UNSIGNED NOT NULL,
    `user_id`     CHAR(36)     NULL DEFAULT NULL,
    `author_name` VARCHAR(50)  NOT NULL,
    `is_staff`    TINYINT(1)   NOT NULL DEFAULT 0,
    `body`        TEXT         NOT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ticket` (`ticket_id`, `created_at`),
    CONSTRAINT `fk_msg_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_msg_user`   FOREIGN KEY (`user_id`)   REFERENCES `users` (`id`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fichiers stockés dans storage/tickets/<ticket_id>/ (hors accès web direct)
CREATE TABLE IF NOT EXISTS `ticket_attachments` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id`     INT UNSIGNED NOT NULL,
    `message_id`    INT UNSIGNED NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `stored_name`   VARCHAR(80)  NOT NULL,
    `mime`          VARCHAR(100) NOT NULL,
    `size`          INT UNSIGNED NOT NULL,
    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_message` (`message_id`),
    CONSTRAINT `fk_att_ticket`  FOREIGN KEY (`ticket_id`)  REFERENCES `tickets` (`id`)         ON DELETE CASCADE,
    CONSTRAINT `fk_att_message` FOREIGN KEY (`message_id`) REFERENCES `ticket_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Personnes ajoutées au ticket par l'auteur (peuvent le consulter et y répondre)
CREATE TABLE IF NOT EXISTS `ticket_participants` (
    `ticket_id` INT UNSIGNED NOT NULL,
    `user_id`   CHAR(36)     NOT NULL,
    PRIMARY KEY (`ticket_id`, `user_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_part_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_part_user`   FOREIGN KEY (`user_id`)   REFERENCES `users` (`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
