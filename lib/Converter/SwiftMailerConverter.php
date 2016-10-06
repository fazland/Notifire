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
        $message = $this->createInstance()
            ->setSubject($email->getSubject())
        ;

        $this->addAddresses($email, $message);
        $this->addParts($email, $message);
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
     */
    protected function addParts(Email $notification, \Swift_Message $email)
    {
        foreach ($notification->getParts() as $part) {
            $email->addPart($part->getContent(), $part->getContentType());
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
}