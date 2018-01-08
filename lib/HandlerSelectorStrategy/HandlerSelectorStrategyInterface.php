<?php declare(strict_types=1);

namespace Fazland\Notifire\HandlerSelectorStrategy;

use Fazland\Notifire\Handler\NotificationHandlerInterface;

interface HandlerSelectorStrategyInterface
{
    /**
     * This method retrieve an handler choosen between handlers passed.
     *
     * @param NotificationHandlerInterface[]
     *
     * @return null|NotificationHandlerInterface
     */
    public function select(array $handlers);
}
