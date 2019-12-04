<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Exception\PartContentTypeMismatchException;
use Fazland\Notifire\Notification\Email\Attachment;
use Fazland\Notifire\Notification\Email\Part;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Notifire's standard representation of an Email as an implementation
 * of {@see NotificationInterface}.
 */
class Email extends AbstractNotification
{
    public const ENCODING_RAW = 'raw';
    public const ENCODING_BASE64 = 'b64';
    public const ENCODING_8BIT = '8bit';
    public const ENCODING_7BIT = '7bit';
    public const ENCODING_QUOTED_PRINTABLE = 'qp';

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
     * @var string[][]
     */
    private $recipientVariables;

    /**
     * Email constructor.
     *
     * @param $handler
     * @param array $options
     */
    public function __construct(string $handler = 'default', array $options = [])
    {
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->from = [];
        $this->subject = '';
        $this->attachments = [];
        $this->parts = [];
        $this->additionalHeaders = [];
        $this->contentType = '';

        $this->tags = [];
        $this->metadata = [];
        $this->recipientVariables = [];

        parent::__construct($handler, $options);
    }

    public static function create(string $handler = 'default', array $options = [])
    {
        return new static($handler, $options);
    }

    /**
     * @return string[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param Attachment[] $attachments
     *
     * @return $this
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param Attachment $attachment
     *
     * @return $this
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @param string[] $bcc
     *
     * @return $this
     */
    public function setBcc(array $bcc): self
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @param string $bcc
     *
     * @return $this
     */
    public function addBcc(string $bcc): self
    {
        $this->bcc[] = $bcc;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @param string[] $cc
     *
     * @return Email
     */
    public function setCc(array $cc): self
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @param string $cc
     *
     * @return $this
     */
    public function addCc(string $cc): self
    {
        $this->cc[] = $cc;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @param string[] $from
     *
     * @return $this
     */
    public function setFrom(array $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $from
     *
     * @return $this
     */
    public function addFrom(string $from): self
    {
        $this->from[] = $from;

        return $this;
    }

    /**
     * @return Part[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * Get a Part object for the specified content type
     * Returns NULL if not set.
     *
     * @param string $contentType
     *
     * @return Part|null
     */
    public function getPart(string $contentType): ?Part
    {
        if (! isset($this->parts[$contentType])) {
            return null;
        }

        return $this->parts[$contentType];
    }

    /**
     * @param Part[] $parts
     *
     * @return $this
     */
    public function setParts(array $parts): self
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
    public function addPart(Part $part, bool $overwrite = false): self
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
     * @param Part $part
     *
     * @return $this
     */
    public function removePart(Part $part): self
    {
        $contentType = $part->getContentType();
        if (! isset($this->parts[$contentType])) {
            return $this;
        }

        unset($this->parts[$contentType]);

        return $this;
    }

    /**
     * Get the plain text body of the mail if set
     * Returns NULL if no text part is present.
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        $part = $this->getPart('text/plain');

        return null !== $part ? $part->getContent() : null;
    }

    /**
     * Add or replace the plain text part of the mail.
     *
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text): self
    {
        return $this->addPart(Part::create($text), true);
    }

    /**
     * Get the html body of the mail if set
     * Returns NULL if no html part is present.
     *
     * @return string|null
     */
    public function getHtml(): ?string
    {
        $part = $this->getPart('text/html');

        return null !== $part ? $part->getContent() : null;
    }

    /**
     * Add or replace the HTML part of the mail.
     *
     * @param string $html
     *
     * @return $this
     */
    public function setHtml(string $html): self
    {
        return $this->addPart(Part::create($html, 'text/html'), true);
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param string[] $to
     *
     * @return $this
     */
    public function setTo(array $to): self
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function addTo(string $to): self
    {
        $this->to[] = $to;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAdditionalHeaders(): array
    {
        return $this->additionalHeaders;
    }

    /**
     * @param string[] $additionalHeaders
     *
     * @return $this
     */
    public function setAdditionalHeaders(array $additionalHeaders): self
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
    public function addAdditionalHeader(string $key, string $value): self
    {
        $this->additionalHeaders[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getAdditionalHeader(string $key): ?string
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
    public function removeAdditionalHeader($key): self
    {
        unset($this->additionalHeaders[$key]);

        return $this;
    }

    /**
     * Check if header $header is set.
     *
     * @param string $header
     *
     * @return bool
     */
    public function hasAdditionalHeader(string $header): bool
    {
        return isset($this->additionalHeaders[$header]);
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
     * Get tags. Only values are returned.
     *
     * @return string[]
     */
    public function getTags(): array
    {
        return \array_values($this->tags);
    }

    /**
     * Replace the tags array. Keys are dropped.
     *
     * @param string[] $tags
     *
     * @return $this
     */
    public function setTags(array $tags): self
    {
        $this->tags = \array_combine($tags, $tags);

        return $this;
    }

    /**
     * Add a tag.
     *
     * @param string $tag
     *
     * @return $this
     */
    public function addTag(string $tag): self
    {
        $this->tags[$tag] = $tag;

        return $this;
    }

    /**
     * Remove a tag if set.
     *
     * @param string $tag
     *
     * @return $this
     */
    public function removeTag(string $tag): self
    {
        unset($this->tags[$tag]);

        return $this;
    }

    /**
     * Get metadata array.
     *
     * @return string[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Replace metadata array.
     *
     * @param string[] $metadata
     *
     * @return $this
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Set a metadata value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addMetadata(string $key, $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Remove a metadata if set.
     *
     * @param string $key
     *
     * @return $this
     */
    public function removeMetadata(string $key): self
    {
        unset($this->metadata[$key]);

        return $this;
    }

    /**
     * Get the recipient variables set.
     *
     * @return array
     */
    public function getRecipientVariables(): array
    {
        return $this->recipientVariables;
    }

    /**
     * Replace the recipient variables set.
     *
     * @param array $recipientVariables
     *
     * @return $this
     */
    public function setRecipientVariables(array $recipientVariables): self
    {
        $this->recipientVariables = $recipientVariables;

        return $this;
    }

    /**
     * Set variable for recipient.
     *
     * @param string $recipient
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function addVariableForRecipient(string $recipient, string $name, string $value): self
    {
        $this->recipientVariables[$recipient][$name] = $value;

        return $this;
    }

    /**
     * Set variables for recipient.
     *
     * @param string   $recipient
     * @param string[] $variables
     *
     * @return $this
     */
    public function addVariablesForRecipient(string $recipient, array $variables): self
    {
        $this->recipientVariables[$recipient] = $variables;

        return $this;
    }

    /**
     * Remove variable for recipient.
     *
     * @param string $recipient
     * @param string $name
     *
     * @return $this
     */
    public function removeVariableForRecipient(string $recipient, string $name): self
    {
        unset($this->recipientVariables[$recipient][$name]);

        return $this;
    }

    /**
     * Remove variables set for recipient.
     *
     * @param string $recipient
     *
     * @return $this
     */
    public function removeVariablesForRecipient(string $recipient): self
    {
        unset($this->recipientVariables[$recipient]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['mailer' => 'default']);
    }
}
