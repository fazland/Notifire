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
     * @var int
     */
    private $priority;

    /**
     * @param string $name
     * @param int $priority
     */
    public function __construct($name = 'default', $priority = 1)
    {
        $this->name = $name;
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
