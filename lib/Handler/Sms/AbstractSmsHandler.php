<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Handler\AbstractNotificationHandler;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;

abstract class AbstractSmsHandler extends AbstractNotificationHandler
{
    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification): bool
    {
        return $notification instanceof Sms;
    }
}
