<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Exception\NotificationFailedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * NotificationTrait adds a standard way to set an instance of {@see EventDispatcherInterface}
 * and to implement the {@see NotificationInterface::send()} method.
 *
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
trait NotificationTrait
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function send()
    {
        $event = new NotifyEvent($this);

        $this->eventDispatcher->dispatch(NotifyEvent::NOTIFY, $event);

        if (! $event->isNotified()) {
            $message = "No handler has been defined for ".get_class($this);
            if (isset($this->config)) {
                $message .= " (".json_encode($this->config).")";
            }

            throw new NotificationFailedException($message);
        }
    }
}
