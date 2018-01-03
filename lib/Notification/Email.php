<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Exception\PartContentTypeMismatchException;
use Fazland\Notifire\Notification\Email\Attachment;
use Fazland\Notifire\Notification\Email\Part;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Notifire's standard representation of an Email as an implementation
 * of {@see NotificationInterface}.
 *
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class Email extends AbstractNotification
{
    const ENCODING_RAW = 'raw';
    const ENCODING_BASE64 = 'b64';
    const ENCODING_8BIT = '8bit';
    const ENCODING_7BIT = '7bit';
    const ENCODING_QUOTED_PRINTABLE = 'qp';

    /**
     * @var string[]
     */
    private $additionalHeaders;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string[]
     */
    private $to;

    /**
     * @var string[]
     */
    private $cc;

    /**
     * @var string[]
     */
    private $bcc;

    /**
     * @var string[]
     */
    private $from;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var Attachment[]
     */
    private $attachments;

    /**
     * @var Part[]
     */
    private $parts;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @var string[]
     */
    private $metadata;

    /**
     * @var array
     */
    private $recipientVariables;

    /**
     * Email constructor.
     *
     * @param $handler
     * @param array $options
     */
    public function __construct($handler = 'default', array $options = [])
    {
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->from = [];
        $this->subject = '';
        $this->attachments = [];
        $this->parts = [];
        $this->additionalHeaders = [];
        $this->contentType = null;

        $this->tags = [];
        $this->metadata = [];
        $this->recipientVariables = [];

        parent::__construct($handler, $options);
    }

    public static function create($handler = 'default', array $options = [])
    {
        return new static($handler, $options);
    }

    /**
     * @return string[]
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param Attachment[] $attachments
     *
     * @return $this
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param Attachment $attachment
     *
     * @return $this
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param string[] $bcc
     *
     * @return $this
     */
    public function setBcc(array $bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @param string $bcc
     *
     * @return $this
     */
    public function addBcc($bcc)
    {
        $this->bcc[] = $bcc;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string[] $cc
     *
     * @return Email
     */
    public function setCc(array $cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @param string $cc
     *
     * @return $this
     */
    public function addCc($cc)
    {
        $this->cc[] = $cc;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string[] $from
     *
     * @return $this
     */
    public function setFrom(array $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $from
     *
     * @return $this
     */
    public function addFrom($from)
    {
        $this->from[] = $from;

        return $this;
    }

    /**
     * @return Part[]
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Get a Part object for the specified content type
     * Returns NULL if not set
     *
     * @param $contentType
     *
     * @return Part|null
     */
    public function getPart($contentType)
    {
        if (! isset($this->parts[$contentType])) {
            return;
        }

        return $this->parts[$contentType];
    }

    /**
     * @param Part[] $parts
     *
     * @return $this
     */
    public function setParts(array $parts)
    {
        $this->parts = [];
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    /**
     * @param Part $part
     * @param bool $overwrite
     *
     * @return $this
     */
    public function addPart(Part $part, $overwrite = false)
    {
        $contentType = $part->getContentType();
        if (isset($this->parts[$contentType]) && ! $overwrite) {
            throw new PartContentTypeMismatchException("A part with content type $contentType has been already added");
        }

        $this->parts[$contentType] = $part;
        $part->setEmail($this);

        return $this;
    }

    /**
     * Add or replace the HTML part of the mail
     *
     * @param $html
     *
     * @return Email
     */
    public function setHtml($html)
    {
        return $this->addPart(Part::create($html, 'text/html'), true);
    }

    /**
     * Add or replace the plain text part of the mail
     *
     * @param $text
     *
     * @return Email
     */
    public function setText($text)
    {
        return $this->addPart(Part::create($text, 'text/plain'), true);
    }

    /**
     * Get the html body of the mail if set
     * Returns NULL if no html part is present
     *
     * @return null|string
     */
    public function getHtml()
    {
        $part = $this->getPart('text/html');

        return null !== $part ? $part->getContent() : null;
    }

    /**
     * Get the plain text body of the mail if set
     * Returns NULL if no text part is present
     *
     * @return null|string
     */
    public function getText()
    {
        $part = $this->getPart('text/plain');

        return null !== $part ? $part->getContent() : null;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string[] $to
     *
     * @return $this
     */
    public function setTo(array $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function addTo($to)
    {
        $this->to[] = $to;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAdditionalHeaders()
    {
        return $this->additionalHeaders;
    }

    /**
     * @param string[] $additionalHeaders
     *
     * @return $this
     */
    public function setAdditionalHeaders($additionalHeaders)
    {
        $this->additionalHeaders = $additionalHeaders;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addAdditionalHeader($key, $value)
    {
        $this->additionalHeaders[$key] = $value;

        return $this;
    }

    public function getAdditionalHeader($key)
    {
        if (! $this->hasAdditionalHeader($key)) {
            return null;
        }

        return $this->additionalHeaders[$key];
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAdditionalHeader($key)
    {
        unset($this->additionalHeaders[$key]);

        return $this;
    }

    /**
     * Check if header $header is set
     *
     * @param $header
     *
     * @return bool
     */
    public function hasAdditionalHeader($header)
    {
        return isset($this->additionalHeaders[$header]);
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
     * Get tags. Only values are returned.
     *
     * @return string[]
     */
    public function getTags()
    {
        return array_values($this->tags);
    }

    /**
     * Replace the tags array. Keys are dropped
     *
     * @param string[] $tags
     *
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = array_combine($tags, $tags);

        return $this;
    }

    /**
     * Add a tag
     *
     * @param string $tag
     *
     * @return $this
     */
    public function addTag($tag)
    {
        $this->tags[$tag] = $tag;

        return $this;
    }

    /**
     * Remove a tag if set
     *
     * @param string $tag
     *
     * @return $this
     */
    public function removeTag($tag)
    {
        unset($this->tags[$tag]);

        return $this;
    }

    /**
     * Get metadata array
     *
     * @return string[]
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Replace metadata array
     *
     * @param string[] $metadata
     *
     * @return $this
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Set a metadata value
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function addMetadata($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Remove a metadata if set
     *
     * @param string $key
     *
     * @return $this
     */
    public function removeMetadata($key)
    {
        unset($this->metadata[$key]);

        return $this;
    }

    /**
     * Get the recipient variables set
     *
     * @return array
     */
    public function getRecipientVariables()
    {
        return $this->recipientVariables;
    }

    /**
     * Replace the recipient variables set
     *
     * @param array $recipientVariables
     *
     * @return $this
     */
    public function setRecipientVariables(array $recipientVariables)
    {
        $this->recipientVariables = $recipientVariables;

        return $this;
    }

    /**
     * Set variable for recipient
     *
     * @param string $recipient
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function addVariableForRecipient($recipient, $name, $value)
    {
        $this->recipientVariables[$recipient][$name] = $value;

        return $this;
    }

    /**
     * Set variables for recipient
     *
     * @param string $recipient
     * @param array $variables
     *
     * @return $this
     */
    public function addVariablesForRecipient($recipient, array $variables)
    {
        $this->recipientVariables[$recipient] = $variables;

        return $this;
    }

    /**
     * Remove variable for recipient
     *
     * @param string $recipient
     * @param string $name
     *
     * @return $this
     */
    public function removeVariableForRecipient($recipient, $name)
    {
        unset($this->recipientVariables[$recipient][$name]);

        return $this;
    }

    /**
     * Remove variables set for recipient
     *
     * @param $recipient
     *
     * @return $this
     */
    public function removeVariablesForRecipient($recipient)
    {
        unset($this->recipientVariables[$recipient]);

        return $this;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mailer' => 'default',
        ]);
    }
}
