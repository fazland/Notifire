<?php

namespace Fazland\Notifire\EventSubscriber;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Main EventSubscriber abstract class.
 * Exposes the notification event listener.
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
abstract class NotifyEventSubscriber implements EventSubscriberInterface
{
    /**
     * Main notification event listener.
     * Throws a {@see NotificationFailedException} if the notification fails.
     *
     * @param NotifyEvent $event
     * @throws NotificationFailedException
     */
    public function notify(NotifyEvent $event)
    {
        $notification = $event->getNotification();

        if (! $this->supports($notification)) {
            return;
        }

        $this->doNotify($notification);
        $event->setNotified();
    }

    /**
     * Checks if this instance supports the $notification object (eg: check class, providers, etc).
     *
     * @param NotificationInterface $notification
     *
     * @return bool
     */
    abstract protected function supports(NotificationInterface $notification);

    /**
     * Sends the notification.
     *
     * @param NotificationInterface $notification
     */
    abstract protected function doNotify(NotificationInterface $notification);

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            NotifyEvent::NOTIFY => ['notify']
        ];
    }
}
