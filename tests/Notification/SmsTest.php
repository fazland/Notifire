<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Sms;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class SmsTest extends TestCase
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
