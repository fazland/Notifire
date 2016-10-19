<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;

abstract class AbstractMailHandler implements NotificationHandlerInterface
{
    private $name;

    /**
     * AbstractMailHandler constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification)
    {
        return $notification instanceof Email;
    }

    public function getName()
    {
        return $this->name;
    }
}
