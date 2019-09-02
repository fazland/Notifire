<?php declare(strict_types=1);

namespace Fazland\Notifire\Manager;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\NotificationInterface;

/**
 * Abstract representation of a NotificationManager.
 */
interface NotificationManagerInterface
{
    /**
     * Add an handler definition.
     *
     * @param NotificationHandlerInterface $handler
     *
     * @return $this
     */
    public function addHandler(NotificationHandlerInterface $handler): self;

    /**
     * Send a notification.
     * Will trigger pre/post/notify events if an event dispatcher is set.
     *
     * @param NotificationInterface $notification
     *
     * @throws NotificationFailedException
     */
    public function notify(NotificationInterface $notification): void;
}
