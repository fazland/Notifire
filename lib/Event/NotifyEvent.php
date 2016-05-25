<?php

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Notifire standard notification event.
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotifyEvent extends Event
{
    const NOTIFY = 'notifire.notify';

    /**
     * @var \Exception
     */
    private $exception;

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

    /**
     * @param NotificationInterface $notification
     */
    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
        $this->notified = false;
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
     * @param bool $notified
     *
     * @return $this
     */
    public function setNotified($notified = true)
    {
        $this->notified = $notified;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNotified()
    {
        return $this->notified;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }






}
