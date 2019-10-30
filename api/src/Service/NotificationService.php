<?php

namespace App\Service;

use App\Entity\Coach;
use App\Entity\Player;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NotificationService
{
    const TYPE_EMAIL = 'EMAIL';
    const TYPE_SMS = 'SMS';
    const TYPE_WHATSAPP = 'WHATSAPP';

    private $mailerService;
    private $notificationServiceStatus;

    public function __construct(MailerService $mailerService, $notificationServiceStatus)
    {
        $this->mailerService = $mailerService;
        $this->notificationServiceStatus = $notificationServiceStatus;
    }

    public function send($user, string $type)
    {
        if(!(bool)$this->notificationServiceStatus) {
            return;
        }

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