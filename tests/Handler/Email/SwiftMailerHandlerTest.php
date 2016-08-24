<?php

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\Notifire\Notification\Email;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class SwiftMailerHandlerTest extends AbstractEmailHandlerTest
{
    /**
     * @var \Swift_Mailer|ObjectProphecy
     */
    private $mailer;

    protected function getHandler()
    {
        $this->mailer = $this->prophesize(\Swift_Mailer::class);

        return new SwiftMailerHandler($this->mailer->reveal());
    }

    /**
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     */
    public function testShouldThrowExceptionIfNotificationFails()
    {
        $email = new Email();
        $email->addTo('info@example.org');

        $this->mailer->send(Argument::type(\Swift_Message::class))
            ->willReturn(0);

        $this->handler->notify($email);
    }

    public function testShouldAddToAddresses()
    {
        $email = new Email();
        $email->addTo('info@example.org');

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertCount(1, $argument->getTo());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldAddCcAddresses()
    {
        $email = new Email();
        $email->addCc('info@example.org');

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertCount(1, $argument->getCc());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldAddBccAddresses()
    {
        $email = new Email();
        $email->addBcc('info@example.org');

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertCount(1, $argument->getBcc());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldAddFromAddresses()
    {
        $email = new Email();
        $email->addFrom('info@example.org');

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertCount(1, $argument->getFrom());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldAddHeaders()
    {
        $email = new Email();
        $email
            ->addBcc('info@example.org')
            ->addAdditionalHeader('X-Additional-Header', 'header_value')
        ;

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertTrue($argument->getHeaders()->has('X-Additional-Header'));
            $this->assertEquals("header_value", $argument->getHeaders()->get('X-Additional-Header')->getFieldBody());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldAddAttachments()
    {
        $email = new Email();
        $email->addAttachment(Email\Attachment::create()->setContent('ATTACHMENT'));

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $children = $argument->getChildren();
            $this->assertCount(1, $children);
            $this->assertEquals('ATTACHMENT', $children[0]->getBody());
            $this->assertEquals('application/octet-stream', $children[0]->getContentType());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldSetFirstPartAsMessageBody()
    {
        $email = new Email();
        $email->addPart(Email\Part::create('BODY PART', 'text/plain'));

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertCount(0, $argument->getChildren());
            $this->assertEquals('text/plain', $argument->getContentType());
            $this->assertEquals('BODY PART', $argument->getBody());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }

    public function testShouldSetMultipartAlternativeIfEmailIsMultipart()
    {
        $email = new Email();
        $email
            ->addPart(Email\Part::create('BODY PART', 'text/plain'))
            ->addPart(Email\Part::create('PART 2', 'text/html'));

        $this->mailer->send(Argument::that(function ($argument) {
            if (! $argument instanceof \Swift_Message) {
                return false;
            }

            $this->assertCount(2, $argument->getChildren());
            $this->assertEquals('multipart/alternative', $argument->getContentType());

            return true;
        }))
            ->willReturn(1);
        $this->handler->notify($email);
    }
}
