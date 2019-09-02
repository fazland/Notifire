<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification\Email;

/**
 * Attachment class for attaching files to a {@see Email}.
 */
class Attachment
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $contentType;

    /**
     * Attachment constructor.
     */
    public function __construct()
    {
        $this->name = 'attachment';
        $this->contentType = 'application/octet-stream';
    }

    /**
     * @return static
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * Creates an instance of {@see Attachment} from an existing file.
     *
     * @param string      $filename
     * @param string|null $contentType
     *
     * @return static
     */
    public static function createFromFile(string $filename, ?string $contentType = null): self
    {
        $instance = new static();
        $instance->content = \file_get_contents($filename);
        $instance->name = \basename($filename);

        if (null !== $contentType) {
            $instance->contentType = $contentType;
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): self
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
