-- ============================================================
--  ORIGIN — Vue staff des tickets : notes internes, historique, réponses prédéfinies
--  À exécuter APRÈS tickets-grade.sql (phpMyAdmin ou mysql CLI)
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

-- Notes internes (jamais visibles par le joueur)
CREATE TABLE IF NOT EXISTS `ticket_notes` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id`    INT UNSIGNED NOT NULL,
    `user_id`      CHAR(36)     NULL DEFAULT NULL,
    `author_name`  VARCHAR(50)  NOT NULL,
    `author_group` VARCHAR(50)  NULL DEFAULT NULL, -- grade au moment de la note
    `author_badge` VARCHAR(20)  NULL DEFAULT NULL,
    `body`         TEXT         NOT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ticket` (`ticket_id`, `created_at`),
    CONSTRAINT `fk_note_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_note_user`   FOREIGN KEY (`user_id`)   REFERENCES `users` (`id`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historique du ticket (création, assignations, changements de statut / priorité / catégorie…)
CREATE TABLE IF NOT EXISTS `ticket_events` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id`  INT UNSIGNED NOT NULL,
    `actor_name` VARCHAR(50)  NULL DEFAULT NULL, -- NULL = système
    `text`       VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ticket` (`ticket_id`, `created_at`),
    CONSTRAINT `fk_event_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Réponses prédéfinies partagées par le staff ({joueur}, {staff} et {ticket} sont remplacés à l'insertion)
CREATE TABLE IF NOT EXISTS `ticket_templates` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(80)  NOT NULL,
    `body`       TEXT         NOT NULL,
    `created_by` VARCHAR(50)  NULL DEFAULT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ticket_templates` (`title`, `body`) VALUES
    ('Prise en charge',      'Bonjour {joueur},\n\nVotre ticket {ticket} a bien été pris en charge par {staff}. Nous revenons vers vous dès que possible.'),
    ('Demande de capture',   'Bonjour {joueur},\n\nPourriez-vous nous fournir une capture d''écran ou une vidéo du problème ? Cela nous aidera à le traiter plus rapidement.'),
    ('Demande transmise',    'Bonjour {joueur},\n\nVotre demande a été transmise à l''équipe concernée. Nous vous tiendrons informé(e) ici.'),
    ('Résolution',           'Bonjour {joueur},\n\nLe problème est résolu de notre côté. N''hésitez pas à ouvrir un nouveau ticket si besoin.\n\n{staff}');

-- Historique des tickets existants : leur création
INSERT INTO `ticket_events` (`ticket_id`, `actor_name`, `text`, `created_at`)
SELECT `id`, `author_name`, 'Ticket ouvert', `created_at` FROM `tickets`;
