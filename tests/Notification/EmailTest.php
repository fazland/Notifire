<?php

namespace Fazland\Tests\Notification;

use Fazland\Notifire\Event\NotifyEvent;
use Fazland\Notifire\Notification\Email;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function testSendShouldDispatchEvent()
    {
        $dispatcher = $this->prophesize(EventDispatcher::class);
        $dispatcher->dispatch(NotifyEvent::NOTIFY, Argument::type(NotifyEvent::class))
            ->shouldBeCalled();

        $email = new Email();
        $email->setEventDispatcher($dispatcher->reveal());

        $email->send();
    }

    /**
     * @expectedException \Fazland\Notifire\Exception\PartContentTypeMismatchException
     */
    public function testAddPartShouldThrowIfTwoPartsWithSameContentTypeHasAdded()
    {
        Email::create()
            ->addPart(Email\Part::create("BlaBla", 'text/html'))
            ->addPart(Email\Part::create('Foo bar', 'text/html'))
            ;
    }
}
