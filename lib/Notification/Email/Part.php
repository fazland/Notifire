<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification\Email;

use Fazland\Notifire\Notification\Email;

/**
 * Part class for adding email's parts to {@see Email}.
 */
class Part
{
    /**
     * @var string|null
     */
    private $content;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string|null
     */
    private $encoding;

    /**
     * @var Email
     */
    private $email;

    public function __construct()
    {
        $this->encoding = null;
    }

    /**
     * @param string|null $content
     * @param string|null $contentType
     *
     * @return static
     */
    public static function create($content = null, $contentType = 'text/plain')
    {
        $instance = new static();

        $instance->content = $content;
        $instance->contentType = $contentType;

        return $instance;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get encoding name.
     *
     * @return string|null
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets encoding for this part.
     *
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     *
     * @return $this
     */
    public function setEmail(Email $email): self
    {
        $this->email = $email;

        return $this;
    }
}
