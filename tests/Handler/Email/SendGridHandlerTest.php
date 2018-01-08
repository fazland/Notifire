<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\Email\SendGridHandler;
use Fazland\Notifire\Notification\Email;
use Prophecy\Argument;
use SendGrid\Response;

class MockSendGridClient extends \SendGrid\Client
{
    public function mail()
    {
    }

    public function send()
    {
    }

    public function post()
    {
    }
}

class SendGridHandlerTest extends AbstractEmailHandlerTest
{
    private $mailer;

    protected function getHandler()
    {
        $this->mailer = $this->prophesize(\SendGrid::class);

        return new SendGridHandler($this->mailer->reveal(), 'www.example.org', 'default');
    }

    public function testShouldThrowExceptionIfNotificationFails()
    {
        $this->expectException(NotificationFailedException::class);
        $email = new Email();
        $email->addTo('info@example.org');

        $response = new Response(500, 'server error');

        $client = $this->prophesize(MockSendGridClient::class);

        $this->mailer->client = $client->reveal();
        $client->mail()->willReturn($client->reveal());
        $client->send()->willReturn($client->reveal());
        $client->post(Argument::type(\Sendgrid\Mail::class))->willReturn($response);

        $this->handler->notify($email);
    }

    public function testShouldNotThrowExceptionWithValidResponseBySendGridApi()
    {
        $email = new Email();
        $email->addTo('info@example.org');

        $response = new Response(204, 'ok');

        $client = $this->prophesize(MockSendGridClient::class);

        $this->mailer->client = $client->reveal();
        $client->mail()->shouldBeCalled()->willReturn($client->reveal());
        $client->send()->shouldBeCalled()->willReturn($client->reveal());
        $client->post(Argument::type(\Sendgrid\Mail::class))->shouldBeCalled()->willReturn($response);

        $this->handler->notify($email);
    }
}
