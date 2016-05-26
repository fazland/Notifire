<?php

namespace Fazland\Notifire;

use Fazland\Notifire\EventSubscriber\NotNotifiedEventSubscriber;
use Fazland\Notifire\EventSubscriber\Email\SwiftMailerHandler;
use Fazland\Notifire\Exception\NotificationAlreadyRegisteredException;
use Fazland\Notifire\Exception\UnregisteredNotificationException;
use Fazland\Notifire\Exception\UnsupportedClassException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Notifire class is a factory of {@see NotificationInterface}.
 *
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * @method static NotificationInterface email(array $options = [])
 * @method static NotificationInterface sms(array $options = [])
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
     * Removes all registered notifications and current {@see EventDispatcherInterface}.
     */
    public static function reset()
    {
        static::$notifications = [];
        static::$dispatcher = null;
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
     * @throws UnsupportedClassException
     */
    public static function addNotification($notificationName, $notificationClass)
    {
        if (isset(static::$notifications[$notificationName])) {
            throw new NotificationAlreadyRegisteredException();
        }

        $notificationInterfaceName = NotificationInterface::class;
        if (! is_subclass_of($notificationClass, $notificationInterfaceName)) {
            $message = "Expected instance of $notificationInterfaceName, got $notificationClass";

            throw new UnsupportedClassException($message);
        }
        
        static::$notifications[$notificationName] = $notificationClass;
    }

    /**
     * Creates new instances of the registered {@see NotificationInterface}
     * with the current instance of {@see EventDispatcherInterface}.
     *
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
        if (! isset($arguments[0])) {
            $arguments[0] = [];
        }

        if (! is_array($arguments[0])) {
            throw new \InvalidArgumentException('Argument 1 should be an array or null');
        }

        return static::factory($name, $arguments[0]);
    }

    /**
     * Configuration shortcut to a base library initialization.
     */
    public static function create()
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new NotNotifiedEventSubscriber());

        $builder = NotifireBuilder::create()
            ->setDispatcher($dispatcher);

        if (class_exists('Swift_Mailer')) {
            $transport = \Swift_SmtpTransport::newInstance('localhost', 25);
            $mailer = \Swift_Mailer::newInstance($transport);
            $dispatcher->addSubscriber(new SwiftMailerHandler($mailer));
            $builder->addNotification('email', Email::class);
        }

        $builder->initialize();
    }
}
