<?php

namespace App;

use App\Document\User;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $twig;
    private $sender;

    public function __construct(Environment $twig, \Swift_Mailer $mailer, $sender)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->sender = $sender;
    }

    public function sendResetPasswordEmail(User $user)
    {
        $message = (new \Swift_Message('Reset password instructions'))
            ->setFrom($this->sender)
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('emails/resetPassword.txt.twig', ['user' => $user]), 'text/plain');

        $this->mailer->send($message);
    }

    public function sendWelcomeEmail(User $user)
    {
        $message = (new \Swift_Message('Invitation to join Twity platform'))
            ->setFrom($this->sender)
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('emails/welcome.txt.twig', ['user' => $user]), 'text/plain');

        $this->mailer->send($message);
    }
}
