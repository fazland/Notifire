<?php declare(strict_types=1);

namespace Fazland\Notifire\EventListener;

use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\Notification\Email;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class TwigProcessor implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onPreNotify(PreNotifyEvent $event): void
    {
        $email = $event->getNotification();
        if (! $email instanceof Email) {
            return;
        }

        foreach ($email->getParts() as $part) {
            $this->processPart($part);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreNotifyEvent::class => ['onPreNotify', 75],
        ];
    }

    protected function processPart(Email\Part $part): void
    {
        if (! $part instanceof Email\TwigTemplatePart) {
            return;
        }

        if (\method_exists($this->twig, 'load')) {
            $template = $this->twig->load($part->getTemplateName());
        } else {
            $template = $this->twig->loadTemplate($part->getTemplateName());
        }

        $email = $part->getEmail();

        if (empty($email->getSubject()) && $template->hasBlock('subject')) {
            $email->setSubject(\trim($template->renderBlock('subject', $part->getVars())));
        }

        $part->setContent($template->render($part->getVars()));
    }
}
