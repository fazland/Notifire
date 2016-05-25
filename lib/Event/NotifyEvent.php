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

    /**
     * Indicates whether the event has been handled
     *
     * @var bool
     */
    private $notified;

    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
        $this->notified = false;
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

    public function setNotified($notified = true)
    {
        $this->notified = $notified;
    }

    public function isNotified()
    {
        return $this->notified;
    }
}
