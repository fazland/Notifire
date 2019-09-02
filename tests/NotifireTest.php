<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests;

use Fazland\Notifire\Exception\NotificationAlreadyRegisteredException;
use Fazland\Notifire\Exception\UnregisteredNotificationException;
use Fazland\Notifire\Exception\UnsupportedClassException;
use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notifire;
use PHPUnit\Framework\TestCase;

class NotifireTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        Notifire::reset();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $manager = $this->prophesize(NotificationManagerInterface::class);
        Notifire::setManager($manager->reveal());
    }

    public function testNotificationAlreadyRegisteredExceptionShouldBeThrownIfAlreadyRegistered(): void
    {
        $this->expectException(NotificationAlreadyRegisteredException::class);
        Notifire::addNotification('email', Email::class);
        Notifire::addNotification('email', Email::class);
    }

    public function testUnsupportedClassExceptionShouldBeThrownIfClassIsNotSupported(): void
    {
        $this->expectException(UnsupportedClassException::class);

        Notifire::addNotification('unsupported', \stdClass::class);
    }

    public function testFactoryShouldThrowUnregisteredNotificationExceptionIfNotFound(): void
    {
        $this->expectException(UnregisteredNotificationException::class);

        Notifire::factory('email');
    }

    public function testFactoryEmailShouldReturnAnInstanceOfEmail(): void
    {
        Notifire::addNotification('email', Email::class);
        $email = Notifire::email();

        self::assertInstanceOf(Email::class, $email);
    }
}
