<?php

namespace Fazland\Notifire\EventSubscriber;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;

class EmailNotificator extends NotifyEventSubscriber
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $mailerName;

    public function __construct(\Swift_Mailer $mailer, $mailerName = 'default')
    {
        $this->mailer = $mailer;
        $this->mailerName = $mailerName;
    }

    /**
     * @inheritDoc
     */
    protected function supports(NotificationInterface $notification)
    {
        if (! $notification instanceof Email) {
            return false;
        }

        $config = $notification->getConfig();
        return $config['provider'] === 'swiftmailer' && $config['mailer'] === $this->mailerName;
    }

    /**
     * @inheritDoc
     */
    protected function doNotify(NotificationInterface $notification)
    {
        /** @var Email $notification */
        /** @var \Swift_Message $email */
        $email = \Swift_Message::newInstance()
            ->setSubject($notification->getSubject());

        $this->addAddresses($notification, $email);
        $this->addParts($notification, $email);
        $this->addAttachments($notification, $email);

        $result = $this->mailer->send($email);
        
        if (0 === $result) {
            throw new NotificationFailedException("Mailer reported all recipient failed");
        }
    }

    /**
     * Add to, cc, bcc and from addresses to the mail object
     *
     * @param Email $notification
     * @param $email
     */
    protected function addAddresses(Email $notification, \Swift_Message $email)
    {
        foreach ($notification->getTo() as $to) {
            $email->addTo($to);
        }

        foreach ($notification->getCc() as $cc) {
            $email->addCc($cc);
        }

        foreach ($notification->getBcc() as $bcc) {
            $email->addBcc($bcc);
        }

        foreach ($notification->getFrom() as $from) {
            $email->addFrom($from);
        }

    }

    /**
     * Add body parts to the message
     *
     * @param Email $notification
     * @param \Swift_Message $email
     */
    protected function addParts(Email $notification, \Swift_Message $email)
    {
        $parts = $notification->getParts();
        if (1 === count($parts)) {
            $part = reset($parts);
            $email->setBody($part->getContent())
                ->setContentType($part->getContentType());

            return;
        }

        foreach ($notification->getParts() as $part) {
            $email->addPart($part->getContent(), $part->getContentType());
        }
    }

    /**
     * Add the attachments to the message
     *
     * @param Email $notification
     * @param \Swift_Message $email
     */
    protected function addAttachments(Email $notification, \Swift_Message $email)
    {
        foreach ($notification->getAttachments() as $attachment) {
            $email->attach(\Swift_Attachment::newInstance($attachment->getContent(), $attachment->getName(), $attachment->getContentType()));
        }
    }

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
