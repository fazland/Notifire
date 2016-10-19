<?php

namespace Fazland\Notifire\Converter;

use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Util\Email\AddressParser;

/**
 * This class is responsible of converting an Email object to
 * a Swift_Message instance
 */
class SwiftMailerConverter
{
    public function convert(Email $email)
    {
        $message = $this->createInstance()->setSubject($email->getSubject());
        $message->setBoundary($boundary = md5(uniqid()));

        $this->addAddresses($email, $message);
        $this->addParts($email, $message, $boundary);
        $this->addHeaders($email, $message);
        $this->addAttachments($email, $message);

        if ($contentType = $email->getContentType()) {
            $message->setContentType($contentType);
        }

        return $message;
    }

    /**
     * Adds to, cc, bcc and from addresses to the mail object.
     *
     * @param Email $notification
     * @param $email
     */
    protected function addAddresses(Email $notification, \Swift_Message $email)
    {
        foreach ($notification->getTo() as $to) {
            $this->addAddress($email, 'to', $to);
        }

        foreach ($notification->getCc() as $cc) {
            $this->addAddress($email, 'cc', $cc);
        }

        foreach ($notification->getBcc() as $bcc) {
            $this->addAddress($email, 'bcc', $bcc);
        }

        foreach ($notification->getFrom() as $from) {
            $this->addAddress($email, 'from', $from);
        }
    }

    protected function addAddress(\Swift_Message $email, $type, $address)
    {
        $method = 'add'.$type;

        $parsed = AddressParser::parse($address);
        $email->$method($parsed['address'], $parsed['personal']);
    }

    /**
     * Adds body parts to the message.
     *
     * @param Email $notification
     * @param \Swift_Message $email
     * @param string $boundary
     */
    protected function addParts(Email $notification, \Swift_Message $email, $boundary)
    {
        foreach ($notification->getParts() as $part) {
            $mimePart = \Swift_MimePart::newInstance($part->getContent(), $part->getContentType());
            $mimePart->setBoundary($boundary);

            if ($encoder = $this->getEncoder($part)) {
                $mimePart->setEncoder($encoder);
            }

            $email->attach($mimePart);
        }
    }

    /**
     * Adds the attachments to the message.
     *
     * @param Email $notification
     * @param \Swift_Message $email
     */
    protected function addAttachments(Email $notification, \Swift_Message $email)
    {
        foreach ($notification->getAttachments() as $attachment) {
            $email->attach(
                \Swift_Attachment::newInstance(
                    $attachment->getContent(),
                    $attachment->getName(),
                    $attachment->getContentType()
                )
            );
        }
    }

    /**
     * Adds the {@see Email::additionalHeaders} to the message.
     *
     * @param Email $notification
     * @param \Swift_Message $email
     */
    protected function addHeaders(Email $notification, \Swift_Message $email)
    {
        $headers = $email->getHeaders();

        foreach ($notification->getAdditionalHeaders() as $key => $value) {
            if ($value instanceof \DateTime) {
                $headers->addDateHeader($key, $value);
            } else {
                $headers->addTextHeader($key, $value);
            }
        }
    }

    /**
     * Create a new instance of Swift_Message
     *
     * @return \Swift_Message
     */
    protected function createInstance()
    {
        return \Swift_Message::newInstance();
    }

    private function getEncoder(Email\Part $part)
    {
        $encoding = $part->getEncoding();
        if (null === $encoding) {
            return null;
        }

        switch ($encoding) {
            case Email::ENCODING_BASE64:
                return \Swift_Encoding::getBase64Encoding();

            case Email::ENCODING_QUOTED_PRINTABLE:
                return \Swift_Encoding::getQpEncoding();

            case Email::ENCODING_8BIT:
                return \Swift_Encoding::get8BitEncoding();

            case Email::ENCODING_7BIT:
                return \Swift_Encoding::get7BitEncoding();

            case Email::ENCODING_RAW:
                return new \Swift_Mime_ContentEncoder_RawContentEncoder();

            default:
                throw new \InvalidArgumentException('Unknown encoding "'.$encoding.'"');
        }
    }
}