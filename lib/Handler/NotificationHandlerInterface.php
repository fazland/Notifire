<?php

namespace Fazland\Notifire\Handler;

use Fazland\Notifire\Notification\NotificationInterface;

/**
 * Represents a notification handler
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
interface NotificationHandlerInterface
{
    /**
     * Checks if this instance supports the $notification object (eg: check class, providers, etc).
     *
     * @param NotificationInterface $notification
     *
     * @return bool
     */
    public function supports(NotificationInterface $notification);

    /**
     * Send the notification
     *
     * @param NotificationInterface $notification
     */
    public function notify(NotificationInterface $notification);
}
