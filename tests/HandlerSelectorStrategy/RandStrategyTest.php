<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\HandlerSelectorStrategy;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\HandlerSelectorStrategy\RandStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class RandStrategyTest extends TestCase
{
    /**
     * @var RandStrategy
     */
    private $strategy;

    public function setUp()
    {
        $this->strategy = new RandStrategy();
    }

    public function testSelectShouldReturnAnHandler()
    {
        eval(<<<EOF
?><?php

namespace Fazland\Notifire\HandlerSelectorStrategy
{
    function mt_rand()
    {
        return 1;
    }
}
EOF
);

        $handlers = [
            new \stdClass(),
            $this->prophesize(NotificationHandlerInterface::class)->reveal(),
            new \stdClass(),
        ];

        $chosen = $this->strategy->select($handlers);
        $this->assertInstanceOf(NotificationHandlerInterface::class, $chosen);
    }

    public function testSelectShouldReturnNullIfEmptyArrayHasPassed()
    {
        $this->assertNull($this->strategy->select([]));
    }
}
