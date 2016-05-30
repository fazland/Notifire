<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\Email\Part;
use Fazland\Notifire\Notification\Email\TwigTemplatePart;

abstract class AbstractMailHandler implements NotificationHandlerInterface
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