<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\EventListener;

use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\EventListener\TwigProcessor;
use Fazland\Notifire\Notification\Email;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigProcessorTest extends TestCase
{
    /**
     * @var FilesystemLoader
     */
    private $loader;

    /**
     * @var Environment
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
        $this->loader = new FilesystemLoader(__DIR__.'/../Fixtures/Template');
        $this->twig = new Environment($this->loader);
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
