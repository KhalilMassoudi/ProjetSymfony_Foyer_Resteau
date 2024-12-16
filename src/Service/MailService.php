<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendReclamationEmail(string $from, string $to, string $subject, string $content)
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($content)
            ->html('<p>' . nl2br($content) . '</p>'); // Optionnel pour du contenu HTML

        $this->mailer->send($email);
    }
}
