<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Handler\Email\AbstractMailHandler;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;
use PHPUnit\Framework\TestCase;

abstract class AbstractEmailHandlerTest extends TestCase
{
    /**
     * @var AbstractMailHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = $this->getHandler();
    }

    public function unsupportedNotificationsDataProvider()
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
    public function testSupportsShouldReturnFalseOnUnsupportedNotifications($notification)
    {
        $this->assertFalse($this->handler->supports($notification));
    }

    abstract protected function getHandler();
}
