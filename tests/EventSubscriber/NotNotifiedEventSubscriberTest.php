<?php

namespace Fazland\Notifire\Tests\EventSubscriber;

use Fazland\Notifire\EventSubscriber\NotNotifiedEventSubscriber;
use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Notification\Email;

class NotNotifiedEventSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     */
    public function testShouldThrowIsEventIsNotNotified()
    {
        $event = new NotifyEvent(Email::create());

        $subscriber = new NotNotifiedEventSubscriber();
        $subscriber->postNotify($event);
    }
}
