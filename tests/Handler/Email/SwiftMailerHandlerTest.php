<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\Email;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class SwiftMailerHandlerTest extends AbstractEmailHandlerTest
{
    /**
     * @var \Swift_Mailer|ObjectProphecy
     */
    private $mailer;

    /**
     * {@inheritdoc}
     */
    protected function getHandler(): NotificationHandlerInterface
    {
        $this->mailer = $this->prophesize(\Swift_Mailer::class);

        return new SwiftMailerHandler($this->mailer->reveal(), 'default');
    }

    public function testShouldThrowExceptionIfNotificationFails(): void
    {
        $this->expectException(NotificationFailedException::class);

        $email = new Email();
        $email->addTo('info@example.org');

        $this->mailer->send(Argument::type(\Swift_Message::class))
            ->willReturn(0)
        ;

        $this->handler->notify($email);
    }

    public function testShouldAddToAddresses(): void
    {
        $email = new Email();
        $email->addTo('info@example.org');

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertCount(1, $argument->getTo());

                return true;
            }))
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldAddCcAddresses(): void
    {
        $email = new Email();
        $email->addCc('info@example.org');

        $this->mailer
            ->send(Argument::that(function ($argument) {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertCount(1, $argument->getCc());

                return true;
            }))
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldAddBccAddresses(): void
    {
        $email = new Email();
        $email->addBcc('info@example.org');

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertCount(1, $argument->getBcc());

                return true;
            }))
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldAddFromAddresses(): void
    {
        $email = new Email();
        $email
            ->addFrom('info@example.org')
            ->addTo('recipient@example.org')
        ;

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertCount(1, $argument->getFrom());

                return true;
            }))
            ->shouldBeCalled()
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldAddHeaders(): void
    {
        $email = new Email();
        $email
            ->addBcc('info@example.org')
            ->addAdditionalHeader('X-Additional-Header', 'header_value')
        ;

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertTrue($argument->getHeaders()->has('X-Additional-Header'));
                self::assertEquals('header_value', $argument->getHeaders()->get('X-Additional-Header')->getFieldBody());

                return true;
            }))
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldAddAttachments(): void
    {
        $email = new Email();
        $email
            ->addTo('recipient@example.org')
            ->addAttachment(Email\Attachment::create()->setContent('ATTACHMENT'))
        ;

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                $children = $argument->getChildren();
                self::assertCount(1, $children);
                self::assertEquals('ATTACHMENT', $children[0]->getBody());
                self::assertEquals('application/octet-stream', $children[0]->getContentType());

                return true;
            }))
            ->shouldBeCalled()
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldSetFirstPartAsMessageBody(): void
    {
        $email = new Email();
        $email
            ->addTo('recipient@example.org')
            ->addPart(Email\Part::create('BODY PART', 'text/plain'))
        ;

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertCount(0, $argument->getChildren());
                self::assertEquals('text/plain', $argument->getContentType());
                self::assertEquals('BODY PART', $argument->getBody());

                return true;
            }))
            ->shouldBeCalled()
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }

    public function testShouldSetMultipartAlternativeIfEmailIsMultipart(): void
    {
        $email = new Email();
        $email
            ->addTo('recipient@example.org')
            ->addPart(Email\Part::create('BODY PART'))
            ->addPart(Email\Part::create('PART 2', 'text/html'))
        ;

        $this->mailer
            ->send(Argument::that(function ($argument): bool {
                if (! $argument instanceof \Swift_Message) {
                    return false;
                }

                self::assertCount(2, $argument->getChildren());
                self::assertEquals('multipart/alternative', $argument->getContentType());

                return true;
            }))
            ->shouldBeCalled()
            ->willReturn(1)
        ;

        $this->handler->notify($email);
    }
}
