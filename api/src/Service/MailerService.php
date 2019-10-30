<?php

namespace App\Service;

class MailerService
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handleEmail($toName, $toEmail)
    {
        $message = (new \Swift_Message('Welcome'))
            ->setFrom('testcllfp@gmail.com')
            ->setTo($toEmail)
            ->setBody(sprintf('Welcome %s', $toName),
                'text/plain'
            )
        ;

        $this->mailer->send($message);
    }

}