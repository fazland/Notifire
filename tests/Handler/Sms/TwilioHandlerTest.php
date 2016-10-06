<?php

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\Sms\TwilioHandler;
use Fazland\Notifire\Notification\Sms;
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
        $s1 = new Sms();
        $s1->setTo([]);

        $s2 = clone $s1;
        $s2->setFrom('+393333333333');

        $s3 = clone $s2;
        $s3->setContent('Foo Bar');

        return [
            [$s1],
            [$s2],
            [$s3],
        ];
    }

    public function right()
    {
        $sms = new Sms();
        $sms
            ->setTo(['+393333333333'])
            ->setFrom('+393333333333')
            ->setContent('Foo Bar')
        ;

        return [
            [$sms],
        ];
    }

    /**
     * @dataProvider withoutTo
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     */
    public function testShouldThrowExceptionWithoutToField($sms)
    {
        $this->notificator->notify($sms);
    }

    /**
     * @dataProvider right
     */
    public function testShouldCallMessagesCreate($sms)
    {
        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->create(['From' => '+393333333333', 'To' => '+393333333333', 'Body' => 'Foo Bar'])->shouldBeCalled();

        $this->notificator->notify($sms);
    }

    /**
     * @dataProvider right
     */
    public function testShouldUseDefaultFromNumber($sms)
    {
        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->create(['From' => '+393333333333', 'To' => '+393333333333', 'Body' => 'Foo Bar'])->shouldBeCalled();

        $this->notificator->setDefaultFrom('+393333333333');
        $this->notificator->notify($sms);
    }
}
