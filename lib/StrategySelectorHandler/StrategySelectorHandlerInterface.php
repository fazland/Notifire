<?php

namespace Fazland\Notifire\StrategySelectorHandler;

use Fazland\Notifire\Handler\NotificationHandlerInterface;

interface StrategySelectorHandlerInterface
{
    /**
     * This method retrieve an handler choosen between handlers passed
     *
     * @param NotificationHandlerInterface[]
     *
     * @return NotificationHandlerInterface
     */
    public function select($handlers);
}
