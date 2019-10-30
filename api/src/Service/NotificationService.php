<?php

namespace App\Service;

use App\Entity\Coach;
use App\Entity\Player;

class NotificationService
{
    const TYPE_EMAIL = 'EMAIL';
    const TYPE_SMS = 'SMS';
    const TYPE_WHATSAPP = 'WHATSAPP';

    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function send($user, string $type)
    {
        switch ($type) {
            case self::TYPE_EMAIL:
                $this->mailerService->handleEmail($user->getName(), $user->getEmail());
                break;

            case self::TYPE_SMS:
            case self::TYPE_WHATSAPP:
                break;

            default:
                $this->mailerService->handleEmail($user->getName(), $user->getEmail());
        }
    }
}