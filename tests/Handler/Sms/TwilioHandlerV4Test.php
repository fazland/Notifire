<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\Sms\TwilioHandler;
use Fazland\Notifire\Notification\Sms;
use Prophecy\Prophecy\ObjectProphecy;

class TwilioHandlerV4Test extends AbstractSmsHandlerTest
{
    /**
     * @var \Services_Twilio|ObjectProphecy
     */
    private $twilio;

    /**
     * {@inheritdoc}
     */
    public function getHandler()
    {
        if (! class_exists(\Services_Twilio::class)) {
            $this->markTestSkipped('Twilio ^4.0 not installed');
        }

        $this->twilio = $this->prophesize(\Services_Twilio::class);

        return new TwilioHandler($this->twilio->reveal());
    }

    /**
     * @dataProvider right
     *
     * @param Sms $sms
     */
    public function testShouldCallMessagesCreate(Sms $sms)
    {
        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->create(['From' => '+393333333333', 'To' => '+393333333333', 'Body' => 'Foo Bar'])->shouldBeCalled();

        $this->handler->notify($sms);
    }

    public function testShouldUseDefaultFromNumber()
    {
        $sms = new Sms();
        $sms
            ->setTo(['+393333333333'])
            ->setContent('Foo Bar')
        ;

        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->create(['From' => '+393334333333', 'To' => '+393333333333', 'Body' => 'Foo Bar'])->shouldBeCalled();

        $this->handler->setDefaultFrom('+393334333333');
        $this->handler->notify($sms);
    }

    /**
     * @dataProvider right
     *
     * @param Sms $sms
     */
    public function testShouldSetMessagingServiceSid(Sms $sms)
    {
        $twilio = $this->twilio->reveal();
        $account = $this->prophesize(\Services_Twilio_Rest_Account::class);
        $messages = $this->prophesize(\Services_Twilio_Rest_Messages::class);

        $twilio->account = $account->reveal();
        $twilio->account->messages = $messages->reveal();

        $messages->create(['From' => '+393333333333', 'To' => '+393333333333', 'MessagingServiceSid' => 'foobar', 'Body' => 'Foo Bar'])->shouldBeCalled();

        $this->handler->setDefaultFrom('+393333333333');
        $this->handler->setMessagingServiceSid('foobar');
        $this->handler->notify($sms);
    }
}
