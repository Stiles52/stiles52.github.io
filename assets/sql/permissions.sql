-- ============================================================
--  ORIGIN — Groupes : apparence + matrice de permissions
--  À exécuter APRÈS users.sql (phpMyAdmin ou mysql CLI)
--  La liste des permissions disponibles est définie dans GroupManager::PERMISSIONS
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

ALTER TABLE `user_groups`
    ADD COLUMN `color` CHAR(7)     NOT NULL DEFAULT '#4b5563' AFTER `badge`, -- couleur de la carte (palette GroupManager::PALETTE)
    ADD COLUMN `icon`  VARCHAR(50) NOT NULL DEFAULT 'users'   AFTER `color`; -- icône Lucide

UPDATE `user_groups` SET `color` = '#ef4444', `icon` = 'crown',       `badge` = 'danger'    WHERE `slug` = 'administrateur';
UPDATE `user_groups` SET `color` = '#f97316', `icon` = 'shield',      `badge` = 'warning'   WHERE `slug` = 'haut-staff';
UPDATE `user_groups` SET `color` = '#a855f7', `icon` = 'scale',       `badge` = 'warning'   WHERE `slug` = 'moderateur';
UPDATE `user_groups` SET `color` = '#ec4899', `icon` = 'scroll-text', `badge` = 'primary'   WHERE `slug` = 'scenariste';
UPDATE `user_groups` SET `color` = '#22c55e', `icon` = 'code',        `badge` = 'success'   WHERE `slug` = 'developpeur';
UPDATE `user_groups` SET `color` = '#4b5563', `icon` = 'user',        `badge` = 'secondary' WHERE `slug` = 'joueur';

CREATE TABLE IF NOT EXISTS `group_permissions` (
    `group_id`   INT UNSIGNED NOT NULL,
    `permission` VARCHAR(50)  NOT NULL,
    PRIMARY KEY (`group_id`, `permission`),
    CONSTRAINT `fk_perm_group` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Matrice initiale (reprise de la maquette de la page permissions)
INSERT IGNORE INTO `group_permissions` (`group_id`, `permission`)
SELECT g.id, p.permission
FROM `user_groups` g
JOIN (
              SELECT 'create-ticket'       AS permission, 'administrateur,haut-staff,moderateur,scenariste,developpeur,joueur' AS slugs
    UNION ALL SELECT 'view-own-tickets',    'administrateur,haut-staff,moderateur,scenariste,developpeur,joueur'
    UNION ALL SELECT 'manage-tickets',      'administrateur,haut-staff,moderateur,scenariste,developpeur'
    UNION ALL SELECT 'view-candidatures',   'administrateur,haut-staff,moderateur'
    UNION ALL SELECT 'manage-candidatures', 'administrateur,haut-staff,moderateur'
    UNION ALL SELECT 'view-users',          'administrateur,haut-staff,moderateur'
    UNION ALL SELECT 'manage-users',        'administrateur'
    UNION ALL SELECT 'manage-groups',       'administrateur'
    UNION ALL SELECT 'manage-permissions',  'administrateur'
    UNION ALL SELECT 'view-logs',           'administrateur,haut-staff,developpeur'
) p ON FIND_IN_SET(g.slug, p.slugs);

-- Remplacé par la permission « manage-users »
ALTER TABLE `user_groups` DROP COLUMN `can_manage_users`;
