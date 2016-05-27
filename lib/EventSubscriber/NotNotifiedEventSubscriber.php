<?php

namespace Fazland\Notifire\EventSubscriber;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Exception\NotificationFailedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This subscriber is responsible to throw an exception in case
 * no subscriber has notified the event, when no email nor sms
 * (or others) has been sent
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotNotifiedEventSubscriber implements EventSubscriberInterface
{
    public function postNotify(NotifyEvent $event)
    {
        if (! $event->isNotified()) {
            $notification = $event->getNotification();

            $message = "No handler has been defined for ".get_class($notification);
            if (method_exists($notification, 'getConfig')) {
                $message .= " (".json_encode($notification->getConfig()).")";
            }

            throw new NotificationFailedException($message);
        }
    }
    
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            NotifyEvent::NOTIFY => ['postNotify', -255]
        ];
    }
}