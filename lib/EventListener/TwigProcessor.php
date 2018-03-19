<?php declare(strict_types=1);

namespace Fazland\Notifire\EventListener;

use Fazland\Notifire\Event\Events;
use Fazland\Notifire\Event\PreNotifyEvent;
use Fazland\Notifire\Notification\Email;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigProcessor implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onPreNotify(PreNotifyEvent $event)
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
            Events::PRE_NOTIFY => ['onPreNotify', 75],
        ];
    }

    protected function processPart(Email\Part $part)
    {
        if (! $part instanceof Email\TwigTemplatePart) {
            return;
        }

        $template = $this->twig->load($part->getTemplateName());
        $email = $part->getEmail();

        if (empty($email->getSubject()) && $template->hasBlock('subject')) {
            $email->setSubject(trim($template->renderBlock('subject', $part->getVars())));
        }

        $part->setContent($template->render($part->getVars()));
    }
}
