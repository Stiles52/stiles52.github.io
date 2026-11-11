-- ============================================================
--  ORIGIN — Grade du staff sur les réponses aux tickets
--  À exécuter APRÈS tickets.sql (phpMyAdmin ou mysql CLI)
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

-- Grade de l'auteur au moment de la réponse (conservé s'il change de groupe ensuite)
ALTER TABLE `ticket_messages`
    ADD COLUMN `author_group` VARCHAR(50) NULL DEFAULT NULL AFTER `is_staff`, -- nom du groupe (ex : Modérateur)
    ADD COLUMN `author_badge` VARCHAR(20) NULL DEFAULT NULL AFTER `author_group`; -- variante CSS badge-glass--*

-- Réponses staff déjà enregistrées : grade actuel de leur auteur
UPDATE `ticket_messages` m
JOIN `users` u        ON u.id = m.user_id
JOIN `user_groups` g  ON g.id = u.group_id
SET m.author_group = g.name, m.author_badge = g.badge
WHERE m.is_staff = 1 AND m.author_group IS NULL;
