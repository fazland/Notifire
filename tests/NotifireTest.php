<?php

namespace Fazland\Notifire\Tests;

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

    /**
     * @expectedException \Fazland\Notifire\Exception\NotificationAlreadyRegisteredException
     */
    public function testNotificationAlreadyRegisteredShouldBeThrownIfAlreadyRegistered()
    {
        Notifire::addNotification('email', Email::class);
        Notifire::addNotification('email', Email::class);
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
