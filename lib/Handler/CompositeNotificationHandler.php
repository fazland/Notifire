<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler;

use Fazland\Notifire\HandlerSelectorStrategy\HandlerSelectorStrategyInterface;
use Fazland\Notifire\Notification\NotificationInterface;

class CompositeNotificationHandler implements NotificationHandlerInterface
{
    /**
     * @var string
     */
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
     * @param string                           $name
     * @param HandlerSelectorStrategyInterface $strategy
     */
    public function __construct(string $name, HandlerSelectorStrategyInterface $strategy)
    {
        $this->name = $name;
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification): bool
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
    public function notify(NotificationInterface $notification): void
    {
        $handlers = $this->getHandlersFor($notification);
        $handler = $this->strategy->select($handlers);

        $handler->notify($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param NotificationHandlerInterface $notificationHandler
     *
     * @return $this
     */
    public function addNotificationHandler(NotificationHandlerInterface $notificationHandler): self
    {
        $this->notificationHandlers[] = $notificationHandler;

        return $this;
    }

    /**
     * @param NotificationInterface $notification
     *
     * @return NotificationHandlerInterface[]
     */
    private function getHandlersFor(NotificationInterface $notification): array
    {
        $handlers = [];
        foreach ($this->notificationHandlers as $notificationHandler) {
            if ($notificationHandler->supports($notification)) {
                $handlers[] = $notificationHandler;
            }
        }

        return $handlers;
    }
}
