<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler;

use Fazland\Notifire\Handler\CompositeNotificationHandler;
use Fazland\Notifire\Handler\Email\AbstractMailHandler;
use Fazland\Notifire\Handler\Sms\TwilioHandler;
use Fazland\Notifire\HandlerSelectorStrategy\HandlerSelectorStrategyInterface;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CompositeNotificationHandlerTest extends TestCase
{
    public function testSupportMustReturnFalseWithANotSupportedNotification()
    {
        $strategy = $this->prophesize(HandlerSelectorStrategyInterface::class);
        $compositeNotificationHandler = new CompositeNotificationHandler('default', $strategy->reveal());

        $notificationHandler = $this->prophesize(AbstractMailHandler::class);
        $notificationHandler->supports(Argument::type(NotificationInterface::class))->willReturn(false);

        $notification = new Sms();

        $compositeNotificationHandler
            ->addNotificationHandler($notificationHandler->reveal())
        ;

        $this->assertFalse($compositeNotificationHandler->supports($notification));
    }

    public function testSupportMustReturnTrueWithASupportedNotification()
    {
        $strategy = $this->prophesize(HandlerSelectorStrategyInterface::class);
        $compositeNotificationHandler = new CompositeNotificationHandler('default', $strategy->reveal());

        $notificationHandler = $this->prophesize(TwilioHandler::class);
        $notificationHandler->supports(Argument::type(NotificationInterface::class))->willReturn(true);

        $notification = new Sms();

        $compositeNotificationHandler
            ->addNotificationHandler($notificationHandler->reveal())
        ;

        $this->assertTrue($compositeNotificationHandler->supports($notification));
    }

    public function testNotifyCallUseRightHandler()
    {
        $notificationHandler = $this->prophesize(TwilioHandler::class);

        $notificationHandler->notify(Argument::type(NotificationInterface::class))->shouldBeCalledTimes(1);
        $notificationHandler->supports(Argument::type(NotificationInterface::class))->willReturn(true);

        $strategy = $this->prophesize(HandlerSelectorStrategyInterface::class);
        $strategy->select(Argument::type('array'))->willReturn($notificationHandler->reveal());

        $compositeNotificationHandler = new CompositeNotificationHandler('default', $strategy->reveal());

        $notification = new Sms();
        $compositeNotificationHandler
            ->addNotificationHandler($notificationHandler->reveal())
        ;

        $compositeNotificationHandler->notify($notification);
    }
}
