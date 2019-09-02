<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Result\Result;
use Fazland\Notifire\Result\ResultSet;

/**
 * Core interface of Notifire notification system. This Interface
 * MUST be implemented in order to register a notification in Notifire.
 */
interface NotificationInterface
{
    /**
     * Implementors SHOULD inform the notification manager
     * about this object.
     */
    public function send(): void;

    /**
     * Set the manager that will send this notification.
     *
     * @param NotificationManagerInterface $manager
     */
    public function setManager(NotificationManagerInterface $manager);

    /**
     * Add a result for this notification.
     *
     * @param Result $result
     *
     * @return $this
     */
    public function addResult(Result $result): self;

    /**
     * Get the ResultSet.
     *
     * @return ResultSet
     */
    public function getResultSet(): ResultSet;

    /**
     * Get handler name.
     *
     * @return string
     */
    public function getHandlerName(): string;
}
