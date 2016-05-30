<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Manager\NotificationManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * NotificationTrait adds a standard way to set an instance of {@see EventDispatcherInterface}
 * and to implement the {@see NotificationInterface::send()} method.
 *
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
trait NotificationTrait
{
    /**
     * @var NotificationManagerInterface
     */
    private $notificationManager = null;

    /**
     * {@inheritdoc}
     */
    public function setManager(NotificationManagerInterface $notificationManager)
    {
        $this->notificationManager = $notificationManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $this->notificationManager->notify($this);
    }
}
