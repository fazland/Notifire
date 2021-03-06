<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification\Email;

class TwigTemplatePart extends Part
{
    /**
     * @var string|null
     */
    private $templateName;

    /**
     * @var array
     */
    private $vars;

    public function __construct(?string $templateName = null, array $vars = [], string $contentType = 'text/plain')
    {
        parent::__construct();

        $this->templateName = $templateName;
        $this->vars = $vars;

        $this->setContentType($contentType);
    }

    public static function create(?string $templateName = null, string $contentType = 'text/plain', array $vars = []): Part
    {
        return new self($templateName, $vars, $contentType);
    }

    /**
     * @return string|null
     */
    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     *
     * @return $this
     */
    public function setTemplateName(string $templateName): self
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     *
     * @return $this
     */
    public function setVars(array $vars): self
    {
        $this->vars = $vars;

        return $this;
    }
}
