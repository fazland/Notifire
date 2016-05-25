<?php

namespace Fazland\Notifire\Tests\EventSubscriber\Sms;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\EventSubscriber\Sms\TwilioHandler;
use Fazland\Notifire\Notification\Sms;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 * @group sms
 */
class TwilioHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $twilio;

    /**
     * @var TwilioHandler
     */
    private $notificator;


    public function setUp()
    {
        $this->twilio = $this->prophesize(\Services_Twilio::class);

        $this->notificator = new TwilioHandler($this->twilio->reveal());
    }

    public function withoutTo()
    {
        $s1 = new Sms;
        $s1->setTo([])
            ->configureOptions([
                'provider' => 'twilio'
            ]);

        $s2 = clone $s1;
        $s2->setFrom('+393333333333');

        $s3 = clone $s2;
        $s3->setContent('Foo Bar');

        return [
            [$s1],
            [$s2],
            [$s3]
        ];
    }

    public function right()
    {
        $sms = new Sms;
        $sms->setTo(['+393333333333'])
            ->setFrom('+393333333333')
            ->setContent('Foo Bar')
            ->configureOptions([
                'provider' => 'twilio'
            ]);

        return [
            [$sms]
        ];
    }

    /**
     * @dataProvider withoutTo
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     */
    public function testShouldThrowExceptionWithoutToField($sms)
    {
        $event = new NotifyEvent($sms);
        $this->notificator->notify($event);

        $this->assertFalse($event->isNotified());
    }
/*
    public function testShouldCallSendMessage()
    {
        $sms = new Sms;
        $sms->setTo(['+393333333333'])
            ->setFrom('+393333333333')
            ->setContent('Foo Bar')
            ->configureOptions([
                'provider' => 'twilio'
            ]);

        $twilio = $this->twilio->reveal();
        $twilio->accounts = $this->prophesize(\Services_Twilio_Rest_Accounts::class);

        $arg = Argument::type('string');
        $twilio->accounts->get($arg)->willReturn(new \Services_Twilio_Rest_Account($twilio, Argument::any()));

        $twilio->account = $twilio->accounts->get($arg);
        $twilio->account->messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account->messages->sendMessage(
            $sms->getFrom(),
            $sms->getTo(),
            $sms->getContent()
        )->shouldBeCalled();

        $this->notificator->notify(new NotifyEvent($sms));
    } */

    /**
     * @dataProvider right
     */
    public function testShouldSetEventAsNotified($sms)
    {
        $twilio = $this->twilio->reveal();
        $twilio->account = new \stdClass();
        $twilio->account->messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $event = new NotifyEvent($sms);
        $this->notificator->notify($event);

        $this->assertTrue($event->isNotified());
    }
}
