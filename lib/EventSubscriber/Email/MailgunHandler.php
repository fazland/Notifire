<?php

namespace Fazland\Notifire\EventSubscriber\Email;

use Fazland\Notifire\EventSubscriber\NotifyEventSubscriber;
use Fazland\Notifire\Exception\IncompleteNotificationException;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Mailgun\Mailgun;

/**
 * Mailgun event subscriber
 * It uses swift mailer to build a mime message that will be sent
 * through the Mailgun APIs
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class MailgunHandler extends NotifyEventSubscriber
{
    /**
     * @var Mailgun
     */
    private $mailgun;

    /**
     * @var string
     */
    private $domain;

    /**
     * {@inheritDoc}
     */
    public function __construct(Mailgun $mailgun, $domain)
    {
        $this->mailgun = $mailgun;
        $this->domain = $domain;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports(NotificationInterface $notification)
    {
        if (! $notification instanceof Email) {
            return false;
        }
        
        $config = $notification->getConfig();
        return 'mailgun' === $config['provider'] && $this->domain === $config['mailer'];
    }

    /**
     * {@inheritDoc}
     */
    protected function doNotify(NotificationInterface $notification)
    {
        if (! class_exists('Swift_Message')) {
            throw new \RuntimeException("You need to install swift mailer to use mailgun transport");
        }

        /** @var Email $notification */
        $message = \Swift_Message::newInstance($notification->getSubject())
            ->setFrom($notification->getFrom());

        foreach ($notification->getParts() as $part) {
            $message->attach(\Swift_MimePart::newInstance($part->getContent(), $part->getContentType()));
        }

        foreach ($notification->getAttachments() as $attachment) {
            $message->attach(\Swift_Attachment::newInstance(
                $attachment->getContent(), $attachment->getName(), $attachment->getContentType()
            ));
        }

        $postData = [];
        $failed = [];
        $success = [];

        $to = array_merge(array_values($message->getTo()), array_values($message->getCc()), array_values($message->getBcc()));
        foreach (array_chunk($to, 1000) as $to_chunk) {
            $data = $postData;
            $data['to'] = $to_chunk;

            $res = $this->mailgun->sendMessage($this->domain, $data, $message->toString());
            if ($res->http_response_code == 200) {
                $success[] = $res;
            } else {
                $failed[] = $res;
            }
        }

        if (count($success) === 0) {
            throw new NotificationFailedException("Sending failed for message {$notification->getSubject()}", $failed);
        } elseif (count($failed) > 0) {
            throw new IncompleteNotificationException("Sending partially failed for message {$notification->getSubject()}", $failed);
        }
    }
}