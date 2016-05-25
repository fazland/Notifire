<?php

namespace Fazland\Notifire\Tests\EventSubscriber\Sms;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Notification\Sms;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 */
class SmsTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldDispatchNotifyEvent()
    {
        $dispatcher = $this->prophesize(EventDispatcher::class);
        $dispatcher->dispatch(NotifyEvent::NOTIFY, Argument::type(NotifyEvent::class))->shouldBeCalled();

        $sms = new Sms();
        $sms->setEventDispatcher($dispatcher->reveal());

        $sms->send();
    }
}
