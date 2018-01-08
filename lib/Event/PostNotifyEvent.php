<?php declare(strict_types=1);

namespace Fazland\Notifire\Event;

use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is dispatched after all the handlers have
 * been notified.
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
