<?php

namespace Fazland\Notifire;

use Fazland\Notifire\Exception\NotificationAlreadyRegisteredException;
use Fazland\Notifire\Exception\UnregisteredNotificationException;
use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * @method static NotificationInterface email(array $options = [])
 */
class Notifire
{
    /**
     * @var EventDispatcherInterface
     */
    protected static $dispatcher;

    /**
     * @var string[]
     */
    public static $notifications;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public static function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Removes all registered notifications.
     */
    public static function reset()
    {
        static::$notifications = [];
        static::$dispatcher = [];
    }

    /**
     * Register the notification to the factory using $notificationName
     * as the key and $notificationClass as the value of an internal array.
     * If $notificationName is already registered a
     * {@see NotificationAlreadyRegisteredException} will be thrown.
     *
     * @param string $notificationName
     * @param string $notificationClass
     *
     * @throws NotificationAlreadyRegisteredException
     */
    public static function addNotification($notificationName, $notificationClass)
    {
        if (isset(static::$notifications[$notificationName])) {
            throw new NotificationAlreadyRegisteredException();
        }

        static::$notifications[$notificationName] = $notificationClass;
    }

    /**
     * @param string $notificationName
     * @param array $options
     *
     * @return NotificationInterface
     *
     * @throws UnregisteredNotificationException
     */
    public static function factory($notificationName, array $options = [])
    {
        if (! isset(static::$notifications[$notificationName])) {
            throw new UnregisteredNotificationException();
        }

        $class = static::$notifications[$notificationName];

        $instance = new $class($options);
        $instance->setEventDispatcher(static::$dispatcher);

        return $instance;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return NotificationInterface
     *
     * @throws UnregisteredNotificationException
     */
    public static function __callStatic($name, $arguments)
    {
        if (! isset ($arguments[0])) {
            $arguments[0] = [];
        }

        if (! is_array($arguments[0])) {
            throw new \InvalidArgumentException('Argument 1 should be an array or null');
        }

        return static::factory($name, $arguments[0]);
    }
}
