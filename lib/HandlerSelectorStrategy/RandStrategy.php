<?php

namespace Fazland\Notifire\HandlerSelectorStrategy;

class RandStrategy implements HandlerSelectorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function select($handlers)
    {
        return $handlers[mt_rand(0, count($handlers) - 1)];
    }
}
