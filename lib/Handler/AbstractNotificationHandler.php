<?php

namespace Fazland\Notifire\Handler;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
abstract class AbstractNotificationHandler implements NotificationHandlerInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name = 'default')
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
