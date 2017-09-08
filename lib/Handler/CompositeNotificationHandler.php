<?php

namespace Fazland\Notifire\Handler;

use Fazland\Notifire\HandlerSelectorStrategy\HandlerSelectorStrategyInterface;
use Fazland\Notifire\Notification\NotificationInterface;

class CompositeNotificationHandler implements NotificationHandlerInterface
{
    private $name;

    /**
     * @var NotificationHandlerInterface[]
     */
    private $notificationHandlers = [];

    /**
     * @var HandlerSelectorStrategyInterface
     */
    private $strategy;

    /**
     * DefaultNotificationHandler constructor.
     *
     * @param $name
     * @param HandlerSelectorStrategyInterface $strategy
     */
    public function __construct($name, HandlerSelectorStrategyInterface $strategy)
    {
        $this->name = $name;
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification)
    {
        /** @var NotificationHandlerInterface $notificationHandler */
        foreach ($this->notificationHandlers as $notificationHandler) {
            if ($notificationHandler->supports($notification)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification, $safe = false)
    {
        $handlers = $this->getHandlersFor($notification);

        if ($safe) {
            while ($handler = $this->strategy->select($handlers)) {
                try {
                    $handler->notify($notification);
                } catch (\Exception $e) {
                    unset($handlers[array_search($handler, $handlers)]);
                    continue;
                }
                return true;
            }
            throw (new NoAvailableHandlerException());
        } else {
            $this->strategy->select($handlers)->notify($notification);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param NotificationHandlerInterface $notificationHandler
     *
     * @return $this
     */
    public function addNotificationHandler(NotificationHandlerInterface $notificationHandler)
    {
        $this->notificationHandlers[] = $notificationHandler;

        return $this;
    }

    /**
     * @param NotificationInterface $notification
     * @return NotificationHandlerInterface[]
     */
    private function getHandlersFor(NotificationInterface $notification)
    {
        $handlers = [];
        foreach ($this->notificationHandlers as $notificationHandler) {
            if ($notificationHandler->supports($notification)) {
                $handlers[] = $notificationHandler;
            }
        }

        return $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        if (empty($this->notificationHandlers)) {
            return false;
        }

        return true;
    }
}
