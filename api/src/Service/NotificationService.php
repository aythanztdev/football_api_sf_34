<?php

namespace App\Service;

use App\Exception\ServiceNotAvailable;

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

    /**
     * @param mixed $user
     * @param string $type
     *
     * @throws ServiceNotAvailable
     */
    public function send($user, string $type)
    {
        if(!(bool)$this->notificationServiceStatus) {
            return;
        }

        switch ($type) {
            case self::TYPE_SMS:
            case self::TYPE_WHATSAPP:
                throw new ServiceNotAvailable('Notifications channel still not available');
                break;

            case self::TYPE_EMAIL:
            default:
                $this->mailerService->handleEmail($user->getName(), $user->getEmail());
        }
    }
}