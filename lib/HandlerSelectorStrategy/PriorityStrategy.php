<?php

namespace Fazland\Notifire\HandlerSelectorStrategy;

use Fazland\Notifire\Handler\NotificationHandlerInterface;

class PriorityStrategy implements HandlerSelectorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function select($handlers)
    {
        $handlers = $this->sortHandlers($handlers);

        return array_shift($handlers);
    }

    /**
     * @param array $handlers
     *
     * @return NotificationHandlerInterface[]
     */
    private function sortHandlers(array $handlers)
    {
        uasort($handlers, function (NotificationHandlerInterface $a, NotificationHandlerInterface $b) {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;
            }

            return ($a->getPriority() < $b->getPriority()) ? 1 : -1;
        });

        return $handlers;
    }
}
