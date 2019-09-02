<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;
use PHPUnit\Framework\TestCase;

abstract class AbstractEmailHandlerTest extends TestCase
{
    /**
     * @var NotificationHandlerInterface
     */
    protected $handler;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->handler = $this->getHandler();
    }

    public function unsupportedNotificationsDataProvider(): iterable
    {
        $sms = new Sms();

        return [
            [$this->prophesize(NotificationInterface::class)->reveal()],
            [$sms],
        ];
    }

    /**
     * @dataProvider unsupportedNotificationsDataProvider
     */
    public function testSupportsShouldReturnFalseOnUnsupportedNotifications(object $notification): void
    {
        self::assertFalse($this->handler->supports($notification));
    }

    /**
     * Prepares the notification handler for the current test.
     *
     * @return NotificationHandlerInterface
     */
    abstract protected function getHandler(): NotificationHandlerInterface;
}
