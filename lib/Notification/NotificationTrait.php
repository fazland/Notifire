<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Result\Result;
use Fazland\Notifire\Result\ResultSet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * NotificationTrait adds a standard way to set an instance of {@see EventDispatcherInterface}
 * and to implement the {@see NotificationInterface::send()} method.
 */
trait NotificationTrait
{
    /**
     * @var NotificationManagerInterface
     */
    private $notificationManager;

    /**
     * @var ResultSet
     */
    private $resultSet;

    /**
     * @var string
     */
    private $handlerName;

    /**
     * {@inheritdoc}
     */
    public function setManager(NotificationManagerInterface $notificationManager): self
    {
        $this->notificationManager = $notificationManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send(): void
    {
        $this->notificationManager->notify($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultSet(): ResultSet
    {
        if (null === $this->resultSet) {
            $this->resultSet = new ResultSet();
        }

        return $this->resultSet;
    }

    /**
     * {@inheritdoc}
     */
    public function addResult(Result $result): NotificationInterface
    {
        $this->getResultSet()->addResult($result);

        return $this;
    }

    /**
     * @param string $handlerName
     *
     * @return $this
     */
    public function setHandlerName(string $handlerName): self
    {
        $this->handlerName = $handlerName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlerName(): string
    {
        return $this->handlerName;
    }
}
