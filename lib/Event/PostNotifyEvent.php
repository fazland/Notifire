<?php declare(strict_types=1);

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Represents a PostNotifyEvent.
 *
 * Dispatched after the handlers have been notified and the notification is sent.
 *
 * If no handler matched the notification this event is not triggered, but an exception is thrown instead.
 */
class PostNotifyEvent extends Event
{
    /**
     * @var NotificationInterface
     */
    private $notification;

    /**
     * @param NotificationInterface $notification
     */
    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification object.
     *
     * @return NotificationInterface
     */
    public function getNotification(): NotificationInterface
    {
        return $this->notification;
    }
}
