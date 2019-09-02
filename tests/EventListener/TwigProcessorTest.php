<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\EventListener;

use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\EventListener\TwigProcessor;
use Fazland\Notifire\Notification\Email;
use PHPUnit\Framework\TestCase;

class TwigProcessorTest extends TestCase
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

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->loader = new \Twig_Loader_Filesystem(__DIR__.'/../Fixtures/Template');
        $this->twig = new \Twig_Environment($this->loader);
        $this->processor = new TwigProcessor($this->twig);
    }

    public function testShouldRenderTwigParts(): void
    {
        $email = new Email();
        $email
            ->addTo('unused@example.org')
            ->addPart(
                Email\TwigTemplatePart::create('template.html.twig', 'text/html')
            )
        ;

        $this->processor->onPreNotify(new PreNotifyEvent($email));

        self::assertEquals('This is the body', $email->getPart('text/html')->getContent());
    }

    public function testShouldAlsoSetSubjectIfTemplateHasBlockSubject(): void
    {
        $email = new Email();
        $email
            ->addTo('unused@example.org')
            ->addPart(
                Email\TwigTemplatePart::create('template_with_subject.html.twig', 'text/html')
            )
        ;

        $this->processor->onPreNotify(new PreNotifyEvent($email));

        self::assertEquals('This is the body', $email->getPart('text/html')->getContent());
        self::assertEquals('This is the subject', $email->getSubject());
    }
}
