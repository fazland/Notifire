<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Converter\SwiftMailerConverter;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Result\Result;
use Fazland\Notifire\Util\Email\AddressParser;

/**
 * SwiftMailer handler
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class SwiftMailerHandler extends AbstractMailHandler
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var SwiftMailerConverter
     */
    private $converter;

    /**
     * @param \Swift_Mailer $mailer
     * @param string $mailerName
     */
    public function __construct(\Swift_Mailer $mailer, $mailerName)
    {
        $this->mailer = $mailer;

        parent::__construct($mailerName);
    }

    public function setConverter(SwiftMailerConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        /** @var Email $notification */
        if (null === $this->converter) {
            $this->converter = new SwiftMailerConverter();
        }

        if (! empty($notification->getTo()) || ! empty($notification->getCc()) || ! empty($notification->getBcc())) {
            $email = $this->converter->convert($notification);
            $result = $this->mailer->send($email);

            $res = new Result('swiftmailer', $this->getName(), $result > 0);
            $res->setResponse($result);
            $notification->addResult($res);

            if (0 === $result) {
                throw new NotificationFailedException('Mailer reported all recipient failed');
            }
        }
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
}
