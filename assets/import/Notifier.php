<?php

/**
 * Notifications par e-mail des joueurs (désactivables dans Paramètres → Préférences).
 * Un échec d'envoi n'interrompt jamais l'action qui a déclenché la notification.
 */
class Notifier
{
    /** @param string $link chemin relatif à APP_URL (ex : /dashboard?page=ticket-view&id=3) */
    public static function notify(?string $userId, string $subject, string $text, string $link): void
    {
        if ($userId === null) {
            return;
        }
        try {
            $user = Database::getInstance()->fetchOne(
                'SELECT pseudo, email, email_notifications FROM users WHERE id = ? AND status = ?',
                [$userId, 'active']
            );
            if (!$user || !$user['email_notifications']) {
                return;
            }

            $url  = rtrim($_ENV['APP_URL'] ?? '', '/') . $link;
            $body = "<p>Bonjour <strong>" . htmlspecialchars($user['pseudo']) . "</strong>,</p>
                <p>" . nl2br(htmlspecialchars($text)) . "</p>
                <p style='text-align:center;margin:32px 0;'>
                    <a href='" . htmlspecialchars($url) . "'
                       style='background:#22d3ee;color:#000;padding:12px 28px;text-decoration:none;
                              font-weight:bold;border-radius:4px;letter-spacing:1px;'>
                        VOIR SUR LE SITE
                    </a>
                </p>
                <p style='font-size:12px;color:#666;'>
                    Vous pouvez désactiver ces notifications dans Paramètres → Préférences.
                </p>";

            (new Mailer())->send($user['email'], "[ORIGIN] $subject", Auth::emailTemplate("[ORIGIN] $subject", $body));
        } catch (\Throwable $e) {
            error_log('[Notifier] ' . $e->getMessage());
        }
    }
}
