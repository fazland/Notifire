<?php

namespace Fazland\Notifire\Tests\HandlerSelectorStrategy;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\HandlerSelectorStrategy\PriorityStrategy;


/**
 * @runTestsInSeparateProcesses
 */
class PriorityStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriorityStrategy
     */
    private $strategy;

    /**
     * @var array
     */
    private $prioritizedHandlers;

    /**
     * @var NotificationHandlerInterface
     */
    private $highPriorityWorkingHandler;

    /**
     * @var NotificationHandlerInterface
     */
    private $middlePriorityWorkingHandler;

    /**
     * @var NotificationHandlerInterface
     */
    private $lowPriorityWorkingHandler;

    /**
     * @var NotificationHandlerInterface
     */
    private $highPriorityNotWorkingHandler;

    /**
     * @var NotificationHandlerInterface
     */
    private $middlePriorityNotWorkingHandler;

    /**
     * @var NotificationHandlerInterface
     */
    private $lowPriorityNotWorkingHandler;


    public function setUp()
    {
        $this->highPriorityWorkingHandler = $this->prophesize(NotificationHandlerInterface::class);
        $this->middlePriorityWorkingHandler = $this->prophesize(NotificationHandlerInterface::class);
        $this->lowPriorityWorkingHandler = $this->prophesize(NotificationHandlerInterface::class);

        $this->highPriorityNotWorkingHandler = $this->prophesize(NotificationHandlerInterface::class);
        $this
            ->highPriorityNotWorkingHandler
            ->notify($this->prophesize(NotificationInterface::class))
            ->will($this->throwException(new \Exception()));

        $this->middlePriorityNotWorkingHandler = $this->prophesize(NotificationHandlerInterface::class);
        $this->middlePriorityNotWorkingHandler
            ->notify($this->prophesize(NotificationInterface::class))
            ->will($this->throwException(new \Exception()));

        $this->lowPriorityNotWorkingHandler = $this->prophesize(NotificationHandlerInterface::class);
        $this->lowPriorityNotWorkingHandler
            ->notify($this->prophesize(NotificationInterface::class))
            ->will($this->throwException(new \Exception()));

        $this->prioritizedHandlers = [
            100 => $this->highPriorityNotWorkingHandler,
            80 => $this->middlePriorityNotWorkingHandler,
            77 => $this->middlePriorityWorkingHandler,
            99 => $this->highPriorityWorkingHandler,
            66 => $this->lowPriorityNotWorkingHandler,
            44 => $this->lowPriorityWorkingHandler,
        ];

        $this->strategy = new PriorityStrategy();
    }

    public function testProperPriorityForHandlers()
    {
        $chosen = $this->strategy->select($this->prioritizedHandlers);
        $this->assertEquals($this->highPriorityWorkingHandler, $chosen);
    }


    public function testNotWorkingHandlerRemovedFromHandlersArray()
    {
        $this->strategy->select($this->prioritizedHandlers);
        $this->assertEqual(false, array_search($this->highPriorityNotWorkingHandler, $this->prioritizedHandlers));
    }


}