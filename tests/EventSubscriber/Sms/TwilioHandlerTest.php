<?php

namespace Fazland\Notifire\Tests\EventSubscriber\Sms;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\EventSubscriber\Sms\TwilioHandler;
use Fazland\Notifire\Notification\Sms;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
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
        $s1->setTo([]);

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
        ;

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
    }

    public function testShouldCallSendMessage()
    {
        $sms = new Sms;
        $sms->setTo(['+393333333333'])
            ->setFrom('+393333333333')
            ->setContent('Foo Bar')
            ;

        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->sendMessage('+393333333333', '+393333333333', 'Foo Bar')->shouldBeCalled();

        $this->notificator->notify(new NotifyEvent($sms));
    }

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

    public function testShouldSetExceptionOnEventIfNotifyPartiallyFail()
    {
        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->sendMessage('+393333333333', '+393333333333', 'Foo Bar')->shouldBeCalled();
        $messages->sendMessage('+393333333333', 'pappagallo', 'Foo Bar')->willThrow(new \Exception())->shouldBeCalled();

        $sms = new Sms;
        $sms->setTo([
                '+393333333333',
                'pappagallo'
            ])
            ->setFrom('+393333333333')
            ->setContent('Foo Bar')
        ;

        $event = new NotifyEvent($sms);

        $this->notificator->notify($event);
        $this->assertNotNull($event->getException());
    }
}
