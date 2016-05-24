<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Event\NotifyEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
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
    }


}
