<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Notification;

use Fazland\Notifire\Manager\NotificationManagerInterface;
use Fazland\Notifire\Notification\Email;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class EmailTest extends TestCase
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
