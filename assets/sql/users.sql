-- ============================================================
--  ORIGIN — Gestion des utilisateurs (groupes, statut, Minecraft)
--  À exécuter APRÈS auth.sql (phpMyAdmin ou mysql CLI)
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

-- Groupes (le nom `groups` est réservé en MySQL 8 → `user_groups`)
CREATE TABLE IF NOT EXISTS `user_groups` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `slug`             VARCHAR(50)     NOT NULL,
    `name`             VARCHAR(50)     NOT NULL,
    `badge`            VARCHAR(20)     NOT NULL DEFAULT 'secondary', -- suffixe CSS badge-glass--*
    `position`         INT             NOT NULL DEFAULT 0,           -- ordre d'affichage (petit = haut)
    `is_default`       TINYINT(1)      NOT NULL DEFAULT 0,           -- groupe attribué à l'inscription
    `can_manage_users` TINYINT(1)      NOT NULL DEFAULT 0,           -- accès à dashboard?page=users
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `user_groups` (`slug`, `name`, `badge`, `position`, `is_default`, `can_manage_users`) VALUES
    ('administrateur', 'Administrateur', 'danger',    10, 0, 1),
    ('haut-staff',     'Haut-Staff',     'warning',   20, 0, 1),
    ('moderateur',     'Modérateur',     'warning',   30, 0, 0),
    ('scenariste',     'Scénariste',     'primary',   40, 0, 0),
    ('developpeur',    'Développeur',    'success',   50, 0, 0),
    ('joueur',         'Joueur',         'secondary', 100, 1, 0);

ALTER TABLE `users`
    ADD COLUMN `group_id`         INT UNSIGNED NULL DEFAULT NULL AFTER `password`,
    ADD COLUMN `minecraft_pseudo` VARCHAR(16)  NULL DEFAULT NULL AFTER `pseudo`,
    ADD COLUMN `status`           ENUM('active','suspended','banned') NOT NULL DEFAULT 'active' AFTER `group_id`,
    ADD COLUMN `last_login_at`    TIMESTAMP    NULL DEFAULT NULL AFTER `email_verified_at`,
    ADD UNIQUE KEY `uq_minecraft` (`minecraft_pseudo`),
    ADD KEY `idx_group`  (`group_id`),
    ADD KEY `idx_status` (`status`),
    ADD CONSTRAINT `fk_user_group` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL;

-- Les comptes existants rejoignent le groupe par défaut
UPDATE `users` SET `group_id` = (SELECT `id` FROM `user_groups` WHERE `is_default` = 1 LIMIT 1) WHERE `group_id` IS NULL;

-- Promouvoir un compte administrateur (adapter le pseudo) :
-- UPDATE `users` SET `group_id` = (SELECT `id` FROM `user_groups` WHERE `slug` = 'administrateur') WHERE `pseudo` = 'Mathis150';
