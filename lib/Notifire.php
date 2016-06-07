<?php

namespace Fazland\Notifire;

use Fazland\Notifire\Exception\NotificationAlreadyRegisteredException;
use Fazland\Notifire\Exception\UnregisteredNotificationException;
use Fazland\Notifire\Exception\UnsupportedClassException;
use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Notifire class is a factory of {@see NotificationInterface}.
 *
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * @method static Email email(array $options = [])
 * @method static Sms sms(array $options = [])
 */
class Notifire
{
    /**
     * @var NotificationManagerInterface
     */
    protected static $manager;

    /**
     * @var string[]
     */
    public static $notifications;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public static function setManager(NotificationManagerInterface $dispatcher)
    {
        static::$manager = $dispatcher;
    }

    /**
     * Removes all registered notifications and current {@see EventDispatcherInterface}.
     */
    public static function reset()
    {
        static::$notifications = [];
        static::$manager = null;
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
        $instance->setManager(static::$manager);

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
        $builder = NotifireBuilder::create();

        if (class_exists('Swift_Mailer')) {
            $transport = \Swift_SmtpTransport::newInstance('localhost', 25);
            $mailer = \Swift_Mailer::newInstance($transport);

            $handler = new SwiftMailerHandler($mailer);
            if (class_exists('Twig_Environment')) {
                $env = new \Twig_Environment(new \Twig_Loader_Filesystem());
                $handler->setTwig($env);
            }

            $builder->addHandler($handler);
            $builder->addNotification('email', Email::class);
        }

        $builder->initialize();
    }
}
