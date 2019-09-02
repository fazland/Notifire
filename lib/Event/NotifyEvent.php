<?php declare(strict_types=1);

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Represents a NotifyEvent.
 *
 * Dispatched just before an handler sends the notification.
 *
 * Notification is cloned before being passed to the event, so it can be used to modify the
 * notification for a single handler.
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
     * @param NotificationInterface        $notification
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
    public function getNotification(): NotificationInterface
    {
        return $this->notification;
    }

    /**
     * @return NotificationHandlerInterface
     */
    public function getHandler(): NotificationHandlerInterface
    {
        return $this->handler;
    }
}
