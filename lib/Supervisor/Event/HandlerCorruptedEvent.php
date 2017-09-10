<?php

namespace Fazland\Notifire\Supervisor\Event;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Symfony\Component\EventDispatcher\Event;

class HandlerCorruptedEvent extends Event
{
    const NAME = 'notifire.handler.corrupted';

    private $handler;

    /**
     * HandlerCorruptedEvent constructor.
     * @param $handler
     */
    public function __construct(NotificationHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return NotificationHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
