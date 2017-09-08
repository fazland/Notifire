<?php

namespace Fazland\Notifire\HandlerSelectorStrategy;


use Fazland\Notifire\Handler\NotificationHandlerInterface;

class PriorityStrategy implements HandlerSelectorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function select(&$handlers)
    {
        /** @var NotificationHandlerInterface $handler */
        foreach ($handlers as $key => $handler) {
            if (! $handler->isAvailable()) {
                unset($handlers[$key]);
                continue;
            }

            return $handler;
        }

//        throw new \NoHandlersAvailable();
        throw new \Exception();
    }

}