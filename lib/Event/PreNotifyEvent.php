<?php declare(strict_types=1);

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Represents a PreNotifyEvent.
 *
 * This event is called before the handlers are checked
 * and notified about this notification.
 *
 * Can be used to add/filter data in notification.
 */
class PreNotifyEvent extends Event
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
