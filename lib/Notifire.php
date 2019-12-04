<?php declare(strict_types=1);

namespace Fazland\Notifire;

use Fazland\Notifire\Exception\NotificationAlreadyRegisteredException;
use Fazland\Notifire\Exception\UnregisteredNotificationException;
use Fazland\Notifire\Exception\UnsupportedClassException;
use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;

/**
 * Notifire class is a factory of {@see NotificationInterface}.
 *
 * @method static Email email(string $handler = 'default', array $options = [])
 * @method static Sms sms(string $handler = 'default', array $options = [])
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
     * @param NotificationManagerInterface $dispatcher
     */
    public static function setManager(NotificationManagerInterface $dispatcher): void
    {
        static::$manager = $dispatcher;
    }

    /**
     * Removes all registered notifications and current {@see EventDispatcherInterface}.
     */
    public static function reset(): void
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
    public static function addNotification(string $notificationName, string $notificationClass): void
    {
        if (isset(static::$notifications[$notificationName])) {
            throw new NotificationAlreadyRegisteredException();
        }

        $notificationInterfaceName = NotificationInterface::class;
        if (! \is_subclass_of($notificationClass, $notificationInterfaceName)) {
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
     * @param string $handler
     * @param array  $options
     *
     * @return NotificationInterface
     *
     * @throws UnregisteredNotificationException
     */
    public static function factory(
        string $notificationName,
        string $handler = 'default',
        array $options = []
    ): NotificationInterface {
        if (! isset(static::$notifications[$notificationName])) {
            throw new UnregisteredNotificationException();
        }

        $class = static::$notifications[$notificationName];

        $instance = new $class($handler, $options);
        $instance->setManager(static::$manager);

        return $instance;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return NotificationInterface
     *
     * @throws UnregisteredNotificationException
     */
    public static function __callStatic(string $name, array $arguments): NotificationInterface
    {
        if (! isset($arguments[0])) {
            $arguments[0] = 'default';
        }

        if (! isset($arguments[1])) {
            $arguments[1] = [];
        }

        if (! \is_string($arguments[0])) {
            throw new \InvalidArgumentException('Argument 1 should be a string or null');
        }

        if (! \is_array($arguments[1])) {
            throw new \InvalidArgumentException('Argument 2 should be an array or null');
        }

        return static::factory($name, $arguments[0], $arguments[1]);
    }

    /**
     * Configuration shortcut to a base library initialization.
     */
    public static function create(): void
    {
        $builder = NotifireBuilder::create();

        if (\class_exists('Swift_Mailer')) {
            $transport = new \Swift_SmtpTransport('localhost', 25);
            $mailer = new \Swift_Mailer($transport);

            $handler = new SwiftMailerHandler($mailer, 'default');
            $builder->addHandler($handler);
            $builder->addNotification('email', Email::class);
        }

        $builder->initialize();
    }
}
