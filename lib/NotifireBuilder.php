<?php

namespace Fazland\Notifire;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A configurable builder for {@see Notifire}.
 *
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

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Add a {@see NotificationInterface} that will be registered in {@see Notifire}
     *
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

    /**
     * Initializes {@see Notifire} class with the current {@see NotificationInterface}
     * and instance of {@see EventDispatcherInterface}
     *
     * @throws Exception\NotificationAlreadyRegisteredException
     * @throws Exception\UnsupportedClassException
     */
    public function initialize()
    {
        Notifire::reset();

        if (null === $this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }

        if (null === $this->notifications) {
            $this->notifications = [];
        }

        foreach ($this->notifications as $notificationName => $notificationClass) {
            Notifire::addNotification($notificationName, $notificationClass);
        }

        Notifire::setEventDispatcher($this->dispatcher);
    }
}
