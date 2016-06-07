<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
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
     * @var string
     */
    private $mailerName;

    /**
     * @param \Swift_Mailer $mailer
     * @param string $mailerName
     */
    public function __construct(\Swift_Mailer $mailer, $mailerName = 'default')
    {
        $this->mailer = $mailer;
        $this->mailerName = $mailerName;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification)
    {
        if (! $notification instanceof Email) {
            return false;
        }

        $config = $notification->getConfig();

        return $config['provider'] === 'swiftmailer' && $config['mailer'] === $this->mailerName;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        /** @var Email $notification */
        /** @var \Swift_Message $email */
        $email = \Swift_Message::newInstance()
            ->setSubject($notification->getSubject())
        ;

        $this->addAddresses($notification, $email);
        $this->addParts($notification, $email);
        $this->addAttachments($notification, $email);

        $result = $this->mailer->send($email);

        if (0 === $result) {
            throw new NotificationFailedException('Mailer reported all recipient failed');
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
        $parts = $notification->getParts();
        if (1 === count($parts)) {
            $part = reset($parts);
            $email->setBody($this->getContent($part))
                ->setContentType($part->getContentType())
            ;

            return;
        }

        foreach ($notification->getParts() as $part) {
            $email->addPart($this->getContent($part), $part->getContentType());
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
