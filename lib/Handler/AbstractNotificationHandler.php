<?php

namespace Fazland\Notifire\Handler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
abstract class AbstractNotificationHandler implements NotificationHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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
        $this->setLogger(new NullLogger());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
