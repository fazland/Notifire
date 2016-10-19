<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Result\Result;
use Fazland\Notifire\Result\ResultSet;
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
     * @var ResultSet
     */
    private $resultSet = null;

    private $handlerName;

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

    /**
     * {@inheritdoc}
     */
    public function getResultSet()
    {
        if (null === $this->resultSet) {
            $this->resultSet = new ResultSet();
        }

        return $this->resultSet;
    }

    /**
     * {@inheritdoc}
     */
    public function addResult(Result $result)
    {
        $this->getResultSet()->addResult($result);
    }

    /**
     * @param $handlerName
     *
     * @return $this
     */
    public function setHandlerName($handlerName)
    {
        $this->handlerName = $handlerName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlerName()
    {
        return $this->handlerName;
    }
}
