<?php

namespace Fazland\Notifire\HandlerSelectorStrategy;

class RandStrategySelectorStrategy implements HandlerSelectorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function select($handlers)
    {
        return $handlers[rand(0, count($handlers) - 1)];
    }
}
