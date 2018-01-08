<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

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
    public function __construct(string $name = 'default')
    {
        $this->name = $name;
        $this->setLogger(new NullLogger());
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
