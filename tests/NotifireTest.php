<?php

namespace Fazland\Notifire\Tests;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notifire;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 */
class NotifireTest extends \PHPUnit_Framework_TestCase
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
