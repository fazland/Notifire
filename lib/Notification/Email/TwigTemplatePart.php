<?php

namespace Fazland\Notifire\Notification\Email;

class TwigTemplatePart extends Part
{
    /**
     * @var string
     */
    private $templateName;

    /**
     * @var array
     */
    private $vars;

    public function __construct($templateName, array $vars = [], $contentType = 'text/plain')
    {
        $this->templateName = $templateName;
        $this->vars = $vars;

        $this->setContentType($contentType);
    }

    public static function create($templateName = null, $vars = [], $contentType = 'text/plain')
    {
        return new self($templateName, $vars, $contentType);
    }

    /**
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     *
     * @return $this
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     *
     * @return $this
     */
    public function setVars(array $vars)
    {
        $this->vars = $vars;

        return $this;
    }
}
