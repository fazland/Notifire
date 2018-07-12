<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\Sms\TwilioHandler;
use Fazland\Notifire\Notification\Sms;
use Prophecy\Prophecy\ObjectProphecy;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;

class TwilioHandlerV5Test extends AbstractSmsHandlerTest
{
    /**
     * @var Client|ObjectProphecy
     */
    private $twilio;

    /**
     * {@inheritdoc}
     */
    public function getHandler()
    {
        if (! class_exists(Client::class)) {
            $this->markTestSkipped('Twilio ^5.0 not installed');
        }

        $this->twilio = $this->prophesize(Client::class);

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
        $messages = $this->prophesize(MessageList::class);

        $twilio->messages = $messages->reveal();

        $messages->create('+393333333333', ['from' => '+393333333333', 'body' => 'Foo Bar'])->shouldBeCalled();

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
        $messages = $this->prophesize(MessageList::class);

        $twilio->messages = $messages->reveal();

        $messages->create('+393333333333', ['from' => '+393334333333', 'body' => 'Foo Bar'])->shouldBeCalled();

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
        $messages = $this->prophesize(MessageList::class);

        $twilio->messages = $messages->reveal();

        $messages->create('+393333333333', [
            'from' => '+393333333333',
            'body' => 'Foo Bar',
            'messagingServiceSid' => 'foobar',
        ])->shouldBeCalled();

        $this->handler->setMessagingServiceSid('foobar');
        $this->handler->notify($sms);
    }
}
