<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Manager;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Event\PostNotifyEvent;
use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Manager\NotificationManager;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationManagerTest extends TestCase
{
    /**
     * @var NotificationManager
     */
    private $manager;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->manager = new NotificationManager();
        $this->manager->setEventDispatcher($this->dispatcher->reveal());
    }

    public function notificationsDataProvider(): iterable
    {
        return [
            [$this->prophesize(NotificationInterface::class)->reveal()],
            [Email::create()],
        ];
    }

    /**
     * @dataProvider notificationsDataProvider
     */
    public function testNotifyShouldThrowIfNoHandlerIsConfiguredForNotification(object $notification): void
    {
        $this->expectException(NotificationFailedException::class);

        $this->manager->setThrowIfNotNotified(true);
        $this->manager->notify($notification);
    }

    public function testShouldDispatchEvents(): void
    {
        $notification = Email::create();
        $notifyArg = Argument::allOf(
            Argument::type(Email::class),
            Argument::that(static function ($arg) use ($notification): bool {
                return $arg !== $notification;
            })
        );

        $handler = $this->prophesize(NotificationHandlerInterface::class);
        $handler->getName()->willReturn('default');
        $handler->supports(Argument::any())->willReturn(true);
        $handler->notify($notifyArg)->shouldBeCalled();

        $handler2 = $this->prophesize(NotificationHandlerInterface::class);
        $handler2->getName()->willReturn('default');
        $handler2->supports(Argument::any())->willReturn(true);
        $handler2->notify($notifyArg);

        $this->dispatcher->dispatch(Argument::type(PreNotifyEvent::class))
            ->shouldBeCalled()
        ;
        $this->dispatcher
            ->dispatch(Argument::that(static function ($arg) use ($notification): bool {
                if (! $arg instanceof NotifyEvent) {
                    return false;
                }

                $not = $arg->getNotification();

                return $not !== $notification;
            }))
            ->shouldBeCalledTimes(2)
        ;

        $this->dispatcher->dispatch(Argument::type(PostNotifyEvent::class))
            ->shouldBeCalled()
        ;

        $this->manager->addHandler($handler->reveal());
        $this->manager->addHandler($handler2->reveal());

        $this->manager->notify($notification);
    }

    public function testShouldSkipNotSupportedHandlers(): void
    {
        $notification = Email::create();

        $handler = $this->prophesize(NotificationHandlerInterface::class);
        $handler->getName()->willReturn('swiftmailer');
        $handler->supports(Argument::any())->willReturn(false);
        $handler->notify(Argument::any())->shouldNotBeCalled();

        $handler2 = $this->prophesize(NotificationHandlerInterface::class);
        $handler2->getName()->willReturn('default');
        $handler2->supports(Argument::any())->willReturn(true);
        $handler2->notify(Argument::any())->shouldBeCalled();

        $this->manager->addHandler($handler->reveal());
        $this->manager->addHandler($handler2->reveal());

        $this->manager->notify($notification);
    }
}
