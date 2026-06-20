<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private string $fromAddress;
    private string $fromName;

    public function __construct()
    {
        $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@origin-rp.fr';
        $this->fromName    = $_ENV['MAIL_FROM_NAME']    ?? 'ORIGIN RP';
    }

    /**
     * Envoie un e-mail HTML selon le driver configuré dans MAIL_DRIVER :
     *   none → ignoré silencieusement (développement sans e-mail)
     *   log  → écrit dans storage/mail-log/ (développement avec preview)
     *   smtp → envoi réel via SMTP (production)
     */
    public function send(string $to, string $subject, string $htmlBody): bool
    {
        return match ($_ENV['MAIL_DRIVER'] ?? 'smtp') {
            'none'  => true,
            'log'   => $this->logToFile($to, $subject, $htmlBody),
            default => $this->sendSmtp($to, $subject, $htmlBody),
        };
    }

    private function sendSmtp(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST']       ?? '';
        $mail->Port       = (int) ($_ENV['SMTP_PORT'] ?? 587);
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME']   ?? '';
        $mail->Password   = $_ENV['SMTP_PASSWORD']   ?? '';
        $mail->SMTPSecure = ($_ENV['SMTP_ENCRYPTION'] ?? 'tls') === 'ssl'
                            ? PHPMailer::ENCRYPTION_SMTPS
                            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        return $mail->send();
    }

    // Sauvegarde le mail en fichier HTML (pratique en dev : pas besoin de SMTP)
    private function logToFile(string $to, string $subject, string $htmlBody): bool
    {
        $dir = __DIR__ . '/../../storage/mail-log';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $dir . '/' . date('Y-m-d_H-i-s') . '_' . substr(md5($to . $subject), 0, 8) . '.html';
        $content  = "<!-- TO: $to | SUBJECT: $subject | SENT: " . date('Y-m-d H:i:s') . " -->\n" . $htmlBody;

        return file_put_contents($filename, $content) !== false;
    }
}
