<?php

namespace Fazland\Notifire\Tests\EventListener;

use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\EventListener\TwigProcessor;
use Fazland\Notifire\Notification\Email;

class TwigProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Twig_LoaderInterface
     */
    private $loader;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TwigProcessor
     */
    private $processor;

    protected function setUp()
    {
        $this->loader = new \Twig_Loader_Filesystem(__DIR__ . '/../Fixtures/Template');
        $this->twig = new \Twig_Environment($this->loader);
        $this->processor = new TwigProcessor($this->twig);
    }

    public function testShouldRenderTwigParts()
    {
        $email = new Email();
        $email
            ->addTo('unused@example.org')
            ->addPart(
                Email\TwigTemplatePart::create('template.html.twig', [], 'text/html')
            );

        $this->processor->onPreNotify(new PreNotifyEvent($email));

        $this->assertEquals('This is the body', $email->getPart('text/html')->getContent());
    }

    public function testShouldAlsoSetSubjectIfTemplateHasBlockSubject()
    {
        $email = new Email();
        $email
            ->addTo('unused@example.org')
            ->addPart(
                Email\TwigTemplatePart::create('template_with_subject.html.twig', [], 'text/html')
            );

        $this->processor->onPreNotify(new PreNotifyEvent($email));

        $this->assertEquals('This is the body', $email->getPart('text/html')->getContent());
        $this->assertEquals('This is the subject', $email->getSubject());
    }
}
