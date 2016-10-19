<?php

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Handler\Email\MailgunHandler;
use Fazland\Notifire\Notification\Email;
use Mailgun\Mailgun;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class MailgunHandlerTest extends AbstractEmailHandlerTest
{
    /**
     * @var Mailgun|ObjectProphecy
     */
    private $mailgun;

    protected function getHandler()
    {
        $resp = new \stdClass();
        $resp->http_response_code = 200;

        $this->mailgun = $this->prophesize(Mailgun::class);
        $this->mailgun->sendMessage(Argument::cetera())->willReturn($resp);

        return new MailgunHandler($this->mailgun->reveal(), 'www.example.org', 'default', 'mailgun');
    }

    public function testShouldAddTags()
    {
        $that = $this;
        $this->mailgun->sendMessage('www.example.org', Argument::type('array'), Argument::cetera())
            ->shouldBeCalled()
            ->will(function ($args) use ($that) {
                $postData = $args[1];

                $that->assertArrayHasKey('o:tag', $postData);

                $resp = new \stdClass();
                $resp->http_response_code = 200;

                return $resp;
            });

        $this->handler->notify(Email::create()->addTag('tag')->addTo('unused'));
    }

    public function testShouldAddMetadata()
    {
        $that = $this;
        $this->mailgun->sendMessage('www.example.org', Argument::type('array'), Argument::cetera())
            ->shouldBeCalled()
            ->will(function ($args) use ($that) {
                $postData = $args[1];

                $that->assertArrayHasKey('v:meta_foo', $postData);
                $that->assertEquals('bar', $postData['v:meta_foo']);

                $resp = new \stdClass();
                $resp->http_response_code = 200;

                return $resp;
            });

        $this->handler->notify(Email::create()->addMetadata('meta_foo', 'bar')->addTo('unused'));
    }

    public function testShouldAddRecipientVariables()
    {
        $that = $this;
        $this->mailgun->sendMessage('www.example.org', Argument::type('array'), Argument::cetera())
            ->shouldBeCalled()
            ->will(function ($args) use ($that) {
                $postData = $args[1];

                $that->assertArrayHasKey('recipient-variables', $postData);
                $that->assertJson($postData['recipient-variables']);

                $resp = new \stdClass();
                $resp->http_response_code = 200;

                return $resp;
            });

        $this->handler->notify(
            Email::create()
                ->addVariablesForRecipient('test@example.com', ['key' => 'value'])
                ->addTo('test@example.com')
        );
    }
}
