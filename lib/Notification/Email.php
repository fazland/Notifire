<?php

namespace Fazland\Notifire\Notification;

use Fazland\Notifire\Notification\Email\Attachment;
use Fazland\Notifire\Notification\Email\Part;
use Fazland\Notifire\Notification\NotificationInterface;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class Email implements NotificationInterface
{
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

    public function __construct()
    {
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->from = [];
        $this->subject = '';
        $this->attachments = [];
        $this->parts = [];
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
     * @param Part[] $parts
     *
     * @return $this
     */
    public function setParts(array $parts)
    {
        $this->parts = $parts;

        return $this;
    }

    /**
     * @param Part $part
     *
     * @return $this
     */
    public function addPart(Part $part)
    {
        $this->parts[] = $part;

        return $this;
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
     * @inheritdoc
     */
    public function send()
    {
        // TODO: Implement send() method.
    }

}
