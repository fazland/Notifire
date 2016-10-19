<?php

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Handler\AbstractNotificationHandler;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
abstract class AbstractSmsHandler extends AbstractNotificationHandler
{
    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification)
    {
        return $notification instanceof Sms;
    }
}
