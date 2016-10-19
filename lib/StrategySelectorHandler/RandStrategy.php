<?php

namespace Fazland\Notifire\StrategySelectorHandler;

class RandStrategy implements StrategySelectorHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function select($handlers)
    {
        return $handlers[rand(0, count($handlers) - 1)];
    }
}
