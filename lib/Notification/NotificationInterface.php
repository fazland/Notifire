<?php

namespace Fazland\Notifire\Notification;
use Fazland\Notifire\Manager\NotificationManagerInterface;

/**
 * Core interface of Notifire notification system. This Interface
 * MUST be implemented in order to register a notification in Notifire.
 *
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
interface NotificationInterface
{
    /**
     * Implementors SHOULD inform the notification manager
     * about this object
     */
    public function send();

    /**
     * Set the manager that will send this notification
     *
     * @param NotificationManagerInterface $manager
     */
    public function setManager(NotificationManagerInterface $manager);
}
