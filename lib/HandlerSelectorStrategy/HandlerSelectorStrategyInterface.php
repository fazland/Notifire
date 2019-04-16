<?php declare(strict_types=1);

namespace Fazland\Notifire\HandlerSelectorStrategy;

use Fazland\Notifire\Handler\NotificationHandlerInterface;

interface HandlerSelectorStrategyInterface
{
    /**
     * This method retrieve an handler chosen between handlers passed.
     *
     * @param NotificationHandlerInterface[]
     *
     * @return NotificationHandlerInterface|null
     */
    public function select(array $handlers);
}
