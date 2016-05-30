<?php

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is triggered just before an handler is notified
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotifyEvent extends Event
{
    /**
     * @var NotificationInterface
     */
    private $notification;

    /**
     * @var NotificationHandlerInterface
     */
    private $handler;

    /**
     * @param NotificationInterface $notification
     * @param NotificationHandlerInterface $handler
     */
    public function __construct(NotificationInterface $notification, NotificationHandlerInterface $handler)
    {
        $this->notification = $notification;
        $this->handler = $handler;
    }

    /**
     * Get the notification object.
     *
     * @return NotificationInterface
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return NotificationHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
