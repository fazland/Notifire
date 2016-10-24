<?php

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\Sms;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
abstract class AbstractSmsHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationHandlerInterface
     */
    protected $handler;

    /**
     * {@inheritdoc]
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
        $s1->setTo([]);

        $s2 = clone $s1;
        $s2->setFrom('+393333333333');

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
