<?php

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
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_NOTIFY => ['onPreNotify', 75]
        ];
    }

    protected function processPart(Email\Part $part)
    {
        if (! $part instanceof Email\TwigTemplatePart) {
            return;
        }

        $part->setContent($this->twig->render($part->getTemplateName(), $part->getVars()));
    }
}