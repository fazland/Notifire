<?php

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

class NotifyEvent extends Event
{
    const NOTIFY = 'notifire.notify';

    /**
     * @var NotificationInterface
     */
    private $notification;

    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification object
     *
     * @return NotificationInterface
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
