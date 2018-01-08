<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\Sms;
use PHPUnit\Framework\TestCase;

abstract class AbstractSmsHandlerTest extends TestCase
{
    /**
     * @var NotificationHandlerInterface
     */
    protected $handler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->handler = $this->getHandler();
    }

    /**
     * @return NotificationHandlerInterface
     */
    abstract public function getHandler();

    public function withoutTo()
    {
        $s1 = new Sms();
        $s1->setFrom('+393333333333');

        $s2 = clone $s1;
        $s2->setTo([]);

        $s3 = clone $s2;
        $s3->setContent('Foo Bar');

        return [
            [$s1],
            [$s2],
            [$s3],
        ];
    }

    public function right()
    {
        $sms = new Sms();
        $sms
            ->setTo(['+393333333333'])
            ->setFrom('+393333333333')
            ->setContent('Foo Bar')
        ;

        return [
            [$sms],
        ];
    }

    /**
     * @dataProvider withoutTo
     *
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     *
     * @param Sms $sms
     */
    public function testShouldThrowNotificationFailedExceptionWithoutToField(Sms $sms)
    {
        $this->handler->notify($sms);
    }
}
