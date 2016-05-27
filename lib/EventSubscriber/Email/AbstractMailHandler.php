<?php

namespace Fazland\Notifire\EventSubscriber\Email;

use Fazland\Notifire\EventSubscriber\NotifyEventSubscriber;
use Fazland\Notifire\Notification\Email\Part;
use Fazland\Notifire\Notification\Email\TwigTemplatePart;

abstract class AbstractMailHandler extends NotifyEventSubscriber
{
    use TwigPartProcessingTrait;

    protected function getContent(Part $part)
    {
        if ($part instanceof TwigTemplatePart) {
            return $this->renderTwigPartContent($part);
        }

        return $part->getContent();
    }
}