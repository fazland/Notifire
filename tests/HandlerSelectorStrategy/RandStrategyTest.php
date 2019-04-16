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

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->strategy = new RandStrategy();
    }

    public function testSelectShouldReturnAnHandler()
    {
        $chosen = $this->strategy->select([
            $this->prophesize(NotificationHandlerInterface::class)->reveal(),
            $this->prophesize(NotificationHandlerInterface::class)->reveal(),
        ]);

        self::assertInstanceOf(NotificationHandlerInterface::class, $chosen);
    }

    public function testSelectShouldReturnNullIfEmptyArrayHasPassed()
    {
        self::assertNull($this->strategy->select([]));
    }
}
