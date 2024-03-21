<?php

namespace App\Service;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{


    public function __construct(MailerInterface $mailer)
    {

    }

    public function sendEmail($content = '<p>See Twig integration for better HTML integration!</p>'): void
    {
        $email = (new Email())
            ->from('cc@example.com')
            ->to('abichou.asmaa@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);
    }
}