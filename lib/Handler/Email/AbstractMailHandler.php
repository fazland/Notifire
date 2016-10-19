<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Handler\AbstractNotificationHandler;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;

abstract class AbstractMailHandler extends AbstractNotificationHandler
{
    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification)
    {
        return $notification instanceof Email;
    }
}
