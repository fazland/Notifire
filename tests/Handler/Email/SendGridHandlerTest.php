<?php

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\Email\SendGridHandler;
use Fazland\Notifire\Notification\Email;
use Prophecy\Argument;
use SendGrid\Response;

/**
 * @author Giovanni Albero <giovanni.albero@fazland.com>
 */
if (version_compare(PHP_VERSION, '5.6', '>=')) {
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
}

/**
 * @requires PHP 5.6
 */
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
        $this->setExpectedException(NotificationFailedException::class);
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

    public function testNoThrowExceptionWithValidResponseBySendGridApi()
    {
        $email = new Email();
        $email->addTo('info@example.org');

        $response = new Response(204, 'ok');

        $client = $this->prophesize(MockSendGridClient::class);

        $this->mailer->client = $client->reveal();
        $client->mail()->willReturn($client->reveal());
        $client->send()->willReturn($client->reveal());
        $client->post(Argument::type(\Sendgrid\Mail::class))->willReturn($response);

        $this->handler->notify($email);
    }
}
