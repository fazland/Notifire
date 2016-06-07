<?php

namespace Fazland\Notifire\Tests\Manager;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Event\PostNotifyEvent;
use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Manager\NotificationManager;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationManager
     */
    private $manager;

    public function setUp()
    {
        $this->manager = new NotificationManager();
    }

    public function notificationsDataProvider()
    {
        return [
            [$this->prophesize(NotificationInterface::class)->reveal()],
            [Email::create([])],
        ];
    }

    /**
     * @dataProvider notificationsDataProvider
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     */
    public function testNotifyShouldThrowIfNoHandlerIsConfiguredForNotification($notification)
    {
        $this->manager->setThrowIfNotNotified(true);
        $this->manager->notify($notification);
    }

    public function testShouldDispatchEvents()
    {
        $notification = Email::create();
        $notifyArg = Argument::allOf(Argument::type(Email::class), Argument::that(function ($arg) use ($notification) { return $arg !== $notification; }));

        $handler = $this->prophesize(NotificationHandlerInterface::class);
        $handler->supports(Argument::any())->willReturn(true);
        $handler->notify($notifyArg)->willReturn();

        $handler2 = $this->prophesize(NotificationHandlerInterface::class);
        $handler2->supports(Argument::any())->willReturn(true);
        $handler2->notify($notifyArg)->willReturn();

        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $dispatcher->dispatch('notifire.pre_notify', Argument::type(PreNotifyEvent::class))
            ->shouldBeCalled();
        $dispatcher->dispatch('notifire.notify', Argument::that(function ($arg) use ($notification) {
            if (! $arg instanceof NotifyEvent) {
                return false;
            }

            $not = $arg->getNotification();

            return $not !== $notification;
        }))
            ->shouldBeCalledTimes(2);
        $dispatcher->dispatch('notifire.post_notify', Argument::type(PostNotifyEvent::class))
            ->shouldBeCalled();

        $this->manager->addHandler($handler->reveal());
        $this->manager->addHandler($handler2->reveal());
        $this->manager->setEventDispatcher($dispatcher->reveal());

        $this->manager->notify($notification);
    }

    public function testShouldSkipNotSupportedHandlers()
    {
        $notification = Email::create();

        $handler = $this->prophesize(NotificationHandlerInterface::class);
        $handler->supports(Argument::any())->willReturn(false);
        $handler->notify(Argument::any())->shouldNotBeCalled();

        $handler2 = $this->prophesize(NotificationHandlerInterface::class);
        $handler2->supports(Argument::any())->willReturn(true);
        $handler2->notify(Argument::any())->shouldBeCalled();

        $this->manager->addHandler($handler->reveal());
        $this->manager->addHandler($handler2->reveal());

        $this->manager->notify($notification);
    }
}
