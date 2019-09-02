<?php declare(strict_types=1);

namespace Fazland\Notifire\HandlerSelectorStrategy;

use Fazland\Notifire\Handler\NotificationHandlerInterface;

class RandStrategy implements HandlerSelectorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function select(array $handlers): ?NotificationHandlerInterface
    {
        if (empty($handlers)) {
            return null;
        }

        return $handlers[\mt_rand(0, \count($handlers) - 1)];
    }
}
