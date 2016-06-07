<?php

namespace Fazland\Notifire\Tests\Handler\Email;

use Fazland\Notifire\Handler\Email\AbstractMailHandler;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;

abstract class AbstractEmailHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractMailHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = $this->getHandler();
    }

    public function unsupportedNotificationsDataProvider()
    {
        return [
            [$this->prophesize(NotificationInterface::class)->reveal()],
            [Email::create(['mailer' => 'no_default'])],
        ];
    }

    /**
     * @dataProvider unsupportedNotificationsDataProvider
     */
    public function testSupportsShouldReturnFalseOnUnsupportedNotifications($notification)
    {
        $this->assertFalse($this->handler->supports($notification));
    }

    public function testShouldRenderTwigTemplateParts()
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $twig->render('template.twig.html', [])->shouldBeCalled();

        $this->handler->setTwig($twig->reveal());

        $email = new Email();
        $email
            ->addTo('unused@example.org')
            ->addPart(Email\TwigTemplatePart::create('template.twig.html'), 'text/html');

        $this->handler->notify($email);
    }

    abstract protected function getHandler();
}
