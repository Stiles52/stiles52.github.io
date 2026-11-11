-- ============================================================
--  ORIGIN — Paramètres du compte
--  À exécuter APRÈS characters.sql (phpMyAdmin ou mysql CLI)
-- ============================================================

-- Le fichier est en UTF-8 : indispensable pour le client mysql Windows (cp850 par défaut)
SET NAMES utf8mb4;

-- Notifications par e-mail (réponse du staff à un ticket, décision / message sur une candidature)
ALTER TABLE `users`
    ADD COLUMN `email_notifications` TINYINT(1) NOT NULL DEFAULT 1 AFTER `email_verified_at`;
