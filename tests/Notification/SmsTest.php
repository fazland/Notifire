<?php

namespace Fazland\Notifire\Tests\Notification;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Manager\NotificationManagerInterface;
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
        $manager = $this->prophesize(NotificationManagerInterface::class);
        $manager->notify(Argument::type(Sms::class))->shouldBeCalled();

        $sms = new Sms();
        $sms->setManager($manager->reveal());

        $sms->send();
    }
}
