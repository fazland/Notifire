<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Result\Result;
use Fazland\Notifire\Result\ResultSet;

/**
 * Core interface of Notifire notification system. This Interface
 * MUST be implemented in order to register a notification in Notifire.
 *
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
interface NotificationInterface
{
    /**
     * Implementors SHOULD inform the notification manager
     * about this object
     */
    public function send();

    /**
     * Set the manager that will send this notification
     *
     * @param NotificationManagerInterface $manager
     */
    public function setManager(NotificationManagerInterface $manager);

    /**
     * Add a result for this notification
     * 
     * @param Result $result
     */
    public function addResult(Result $result);

    /**
     * Get the ResultSet
     * 
     * @return ResultSet
     */
    public function getResultSet();
}
