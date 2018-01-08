<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notifire;
use PHPUnit\Framework\TestCase;

class NotifireTest extends TestCase
{
    protected function tearDown()
    {
        Notifire::reset();
    }

    protected function setUp()
    {
        $manager = $this->prophesize(NotificationManagerInterface::class);
        Notifire::setManager($manager->reveal());
    }

    /**
     * @expectedException \Fazland\Notifire\Exception\NotificationAlreadyRegisteredException
     */
    public function testNotificationAlreadyRegisteredExceptionShouldBeThrownIfAlreadyRegistered()
    {
        Notifire::addNotification('email', Email::class);
        Notifire::addNotification('email', Email::class);
    }

    /**
     * @expectedException \Fazland\Notifire\Exception\UnsupportedClassException
     */
    public function testUnsupportedClassExceptionShouldBeThrownIfClassIsNotSupported()
    {
        Notifire::addNotification('unsupported', \stdClass::class);
    }

    /**
     * @expectedException \Fazland\Notifire\Exception\UnregisteredNotificationException
     */
    public function testFactoryShouldThrowUnregisteredNotificationExceptionIfNotFound()
    {
        Notifire::factory('email');
    }

    public function testFactoryEmailShouldReturnAnInstanceOfEmail()
    {
        Notifire::addNotification('email', Email::class);
        $email = Notifire::email();

        $this->assertInstanceOf(Email::class, $email);
    }
}
