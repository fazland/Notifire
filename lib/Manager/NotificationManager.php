<?php declare(strict_types=1);

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
 * and is responsible to dispatch events and trigger send commands.
 */
class NotificationManager implements NotificationManagerInterface
{
    /**
     * @var NotificationHandlerInterface[]
     */
    private $handlers;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $throwIfNotNotified;

    public function __construct()
    {
        $this->handlers = [];
        $this->throwIfNotNotified = false;
    }

    /**
     * Add an handler definition.
     *
     * @param NotificationHandlerInterface $handler
     *
     * @return NotificationManagerInterface
     */
    public function addHandler(NotificationHandlerInterface $handler): NotificationManagerInterface
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * Set an event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return NotificationManagerInterface
     */
    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher = null): NotificationManagerInterface
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification): void
    {
        $notified = false;
        $this->eventDispatcher->dispatch(new PreNotifyEvent($notification));

        foreach ($this->handlers as $handler) {
            if ($notification->getHandlerName() !== $handler->getName()) {
                continue;
            }
            $notified = $this->handle($notification, $handler) || $notified;
        }

        if ($this->throwIfNotNotified && ! $notified) {
            $message = 'No handler has been defined for '.\get_class($notification);
            if (\method_exists($notification, 'getConfig')) {
                $message .= ' ('.\json_encode($notification->getConfig()).')';
            }

            throw new NotificationFailedException($message);
        }

        $this->eventDispatcher->dispatch(new PostNotifyEvent($notification));
    }

    /**
     * Set the manager to throw an exception if no handler
     * has matched the notification.
     *
     * @param bool $throwIfNotNotified
     *
     * @return NotificationManagerInterface
     */
    public function setThrowIfNotNotified(bool $throwIfNotNotified): NotificationManagerInterface
    {
        $this->throwIfNotNotified = $throwIfNotNotified;

        return $this;
    }

    /**
     * Checks if handler supports this notification and eventually
     * send the notification.
     *
     * @param NotificationInterface        $notification
     * @param NotificationHandlerInterface $handler
     *
     * @return bool
     */
    protected function handle(NotificationInterface $notification, NotificationHandlerInterface $handler): bool
    {
        if (! $handler->supports($notification)) {
            return false;
        }

        $cloned = clone $notification;

        $event = new NotifyEvent($cloned, $handler);
        $this->eventDispatcher->dispatch($event);

        $handler->notify($cloned);

        foreach ($cloned->getResultSet()->all() as $result) {
            $notification->addResult($result);
        }

        return true;
    }
}
