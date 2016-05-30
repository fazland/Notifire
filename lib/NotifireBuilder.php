<?php

namespace Fazland\Notifire;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Manager\NotificationManager;
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
     * @var \SplObjectStorage|NotificationHandlerInterface[]
     */
    protected $handlers;

    /**
     * @var string[]
     */
    protected $notifications;

    public function __construct()
    {
        $this->notifications = [];
        $this->handlers = new \SplObjectStorage();
    }

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
     * Add a notification handler to the manager
     *
     * @param NotificationHandlerInterface $handler
     *
     * @return $this
     */
    public function addHandler(NotificationHandlerInterface $handler)
    {
        $this->handlers->attach($handler);

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

        $manager = new NotificationManager();
        foreach ($this->handlers as $handler) {
            $manager->addHandler($handler);
        }

        if (null !== $this->dispatcher) {
            $manager->setEventDispatcher($this->dispatcher);
        }

        foreach ($this->notifications as $notificationName => $notificationClass) {
            Notifire::addNotification($notificationName, $notificationClass);
        }

        Notifire::setManager($manager);
    }
}
