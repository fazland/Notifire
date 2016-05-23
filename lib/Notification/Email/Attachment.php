<?php

namespace Fazland\Notifire\Notification\Email;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
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

    public function __construct()
    {
        $this->name = 'attachment';
        $this->contentType = 'application/octet-stream';
    }

    public static function create()
    {
        return new static();
    }

    public static function createFromFile($filename, $contentType = null)
    {
        $instance = new static();
        $instance->content = file_get_contents($filename);
        $instance->name = basename($filename);

        if (null !== $contentType) {
            $instance->contentType = $contentType;
        }

        return $instance;
    }

    /**
     * @return string
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
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


}
