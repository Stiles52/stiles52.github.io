-- ============================================================
--  ORIGIN — Schéma d'authentification
--  Clés primaires en UUID v4 (CHAR 36), générées côté PHP
--  Exécuter dans phpMyAdmin ou via mysql CLI
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id`                CHAR(36)        NOT NULL,
    `pseudo`            VARCHAR(50)     NOT NULL,
    `email`             VARCHAR(255)    NOT NULL,
    `password`          VARCHAR(255)    NOT NULL,
    `email_verified_at` TIMESTAMP       NULL DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pseudo` (`pseudo`),
    UNIQUE KEY `uq_email`  (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions utilisateurs (remplace les sessions PHP côté serveur)
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id`         CHAR(36)      NOT NULL,
    `user_id`    CHAR(36)      NOT NULL,
    `token`      VARCHAR(128)  NOT NULL,
    `ip_address` VARCHAR(45)   DEFAULT NULL,
    `user_agent` VARCHAR(512)  DEFAULT NULL,
    `remember`   TINYINT(1)    NOT NULL DEFAULT 0,
    `expires_at` TIMESTAMP     NOT NULL,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_token`   (`token`),
    KEY        `idx_user`   (`user_id`),
    CONSTRAINT `fk_sess_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tokens de vérification d'e-mail (expire 24h)
CREATE TABLE IF NOT EXISTS `email_verifications` (
    `id`         CHAR(36)      NOT NULL,
    `user_id`    CHAR(36)      NOT NULL,
    `token`      VARCHAR(64)   NOT NULL,
    `expires_at` TIMESTAMP     NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_token`  (`token`),
    KEY        `idx_user`  (`user_id`),
    CONSTRAINT `fk_verif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tokens de réinitialisation de mot de passe (expire 1h)
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`         CHAR(36)      NOT NULL,
    `user_id`    CHAR(36)      NOT NULL,
    `token`      VARCHAR(64)   NOT NULL,
    `expires_at` TIMESTAMP     NOT NULL,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_token`  (`token`),
    KEY        `idx_user`  (`user_id`),
    CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
