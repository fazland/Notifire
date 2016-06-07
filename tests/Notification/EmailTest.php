<?php

namespace Fazland\Notifire\Tests\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use Prophecy\Argument;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function testSendShouldCallNotificationManager()
    {
        $manager = $this->prophesize(NotificationManagerInterface::class);
        $manager->notify(Argument::type(Email::class))->shouldBeCalled();

        $email = new Email();
        $email->setManager($manager->reveal());

        $email->send();
    }

    /**
     * @expectedException \Fazland\Notifire\Exception\PartContentTypeMismatchException
     */
    public function testAddPartShouldThrowIfTwoPartsWithSameContentTypeHasAdded()
    {
        Email::create()
            ->addPart(Email\Part::create('BlaBla', 'text/html'))
            ->addPart(Email\Part::create('Foo bar', 'text/html'))
            ;
    }
}
