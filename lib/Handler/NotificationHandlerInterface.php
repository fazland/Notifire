<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler;

use Fazland\Notifire\Notification\NotificationInterface;

/**
 * Represents a notification handler.
 */
interface NotificationHandlerInterface
{
    /**
     * Checks if this instance supports the $notification object (eg: check class, providers, etc).
     *
     * @param NotificationInterface $notification
     *
     * @return bool
     */
    public function supports(NotificationInterface $notification): bool;

    /**
     * Send the notification.
     *
     * @param NotificationInterface $notification
     */
    public function notify(NotificationInterface $notification): void;

    /**
     * Return handler name.
     *
     * @return string
     */
    public function getName(): string;
}
