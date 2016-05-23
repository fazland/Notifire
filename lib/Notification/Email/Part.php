<?php

namespace Fazland\Notifire\Notification\Email;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class Part
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $contentType;

    public static function create($content = null, $contentType = null)
    {
        $instance = new static();

        $instance->content = $content;
        $instance->contentType = $contentType;
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


}
