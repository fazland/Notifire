<?php

namespace Fazland\Notifire\Tests\EventListener;


use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\EventListener\TwigProcessor;
use Fazland\Notifire\Notification\Email;

class TwigProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldRenderTwigParts()
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $twig->render('template.twig.html', [])->shouldBeCalled();

        $processor = new TwigProcessor($twig->reveal());

        $email = new Email();
        $email
            ->addTo('unused@example.org')
            ->addPart(Email\TwigTemplatePart::create('template.twig.html'), 'text/html');

        $processor->onPreNotify(new PreNotifyEvent($email));
    }
}
