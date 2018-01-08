<?php declare(strict_types=1);

namespace Fazland\Notifire\HandlerSelectorStrategy;

class RandStrategy implements HandlerSelectorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function select(array $handlers)
    {
        if (empty($handlers)) {
            return null;
        }

        return $handlers[mt_rand(0, count($handlers) - 1)];
    }
}
