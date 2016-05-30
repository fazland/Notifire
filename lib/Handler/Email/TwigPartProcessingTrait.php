<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Notification\Email\TwigTemplatePart;

trait TwigPartProcessingTrait
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setTwig(\Twig_Environment $twig = null)
    {
        $this->twig = $twig;
    }
    
    protected function renderTwigPartContent(TwigTemplatePart $part)
    {
        return $this->twig->render($part->getTemplateName(), $part->getVars());
    }
}
