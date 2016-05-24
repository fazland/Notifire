<?php

namespace Fazland\Notifire;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 */
class NotifireBuilder
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var string[]
     */
    protected $notifications;


    public static function create()
    {
        return new static();
    }

    /**
     * @param string $notificationName
     * @param string $notificationClass
     *
     * @return $this
     */
    public function addNotification($notificationName, $notificationClass)
    {
        $this->notifications[$notificationName] = $notificationClass;

        return $this;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     *
     * @return $this
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function initialize()
    {
        Notifire::reset();

        if (null === $this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }

        foreach ($this->notifications as $notificationName => $notificationClass) {
            Notifire::addNotification($notificationName, $notificationClass);
        }

        Notifire::setEventDispatcher($this->dispatcher);
    }
}
