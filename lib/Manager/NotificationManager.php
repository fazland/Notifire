<?php

namespace Fazland\Notifire\Manager;

use Fazland\Notifire\Event\Events;
use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Event\PostNotifyEvent;
use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * NotificationManager holds the references to the handlers
 * and is responsible to dispatch events and trigger send commands
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotificationManager implements NotificationManagerInterface
{
    /**
     * @var NotificationHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher = null;

    /**
     * Add an handler definition
     *
     * @param NotificationHandlerInterface $handler
     *
     * @return $this
     */
    public function addHandler(NotificationHandlerInterface $handler)
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * Set an event dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Send a notification.
     * Will trigger pre/post/notify events if an event dispatcher is set
     *
     * @param NotificationInterface $notification
     *
     * @throws NotificationFailedException
     */
    public function notify(NotificationInterface $notification)
    {
        $notified = false;
        $this->dispatch(Events::PRE_NOTIFY, PreNotifyEvent::class, $notification);
        
        foreach ($this->handlers as $handler) {
            $notified = $this->handle($notification, $handler) || $notified;
        }
        
        if (! $notified) {
            $message = "No handler has been defined for ".get_class($notification);
            if (method_exists($notification, 'getConfig')) {
                $message .= " (".json_encode($notification->getConfig()).")";
            }

            throw new NotificationFailedException($message);
        }

        $this->dispatch(Events::POST_NOTIFY, PostNotifyEvent::class, $notification);
    }

    /**
     * Checks if handler supports this notification and eventually
     * send the notification
     * 
     * @param NotificationInterface $notification
     * @param NotificationHandlerInterface $handler
     *
     * @return bool
     */
    protected function handle(NotificationInterface $notification, NotificationHandlerInterface $handler)
    {
        if (! $handler->supports($notification)) {
            return false;
        }

        $notification = clone $notification;
        $this->dispatch(Events::NOTIFY, NotifyEvent::class, $notification);

        $handler->notify($notification);
        return true;
    }

    /**
     * Create a new Event object and trigger it if eventDispatcher is set
     *
     * @param string $event
     * @param string $class
     * @param NotificationInterface $notification
     */
    protected function dispatch($event, $class, NotificationInterface $notification)
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $e = new $class($notification);
        $this->eventDispatcher->dispatch($event, $e);
    }
}
