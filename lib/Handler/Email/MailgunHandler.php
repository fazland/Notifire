<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Result\Result;
use Mailgun\Mailgun;

/**
 * Mailgun handler
 * It uses swift mailer to build a mime message that will be sent
 * through the Mailgun APIs
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class MailgunHandler extends AbstractMailHandler
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
     * @var string
     */
    private $mailerName;

    /**
     * {@inheritdoc}
     */
    public function __construct(Mailgun $mailgun, $domain, $mailerName)
    {
        $this->mailgun = $mailgun;
        $this->domain = $domain;
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

        return $this->mailerName === $config['mailer'];
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        if (! class_exists('Swift_Message')) {
            throw new \RuntimeException('You need to install swift mailer to use mailgun transport');
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

        $postData = [
            'o:tag' => $notification->getTags(),
        ];

        foreach ($notification->getMetadata() as $key => $value) {
            $postData['v:'.$key] = $value;
        }

        if ($recipientVariables = $notification->getRecipientVariables()) {
            $postData['recipient-variables'] = json_encode($recipientVariables);
        }

        $failed = [];
        $success = [];

        $to = array_merge(array_values($notification->getTo()), array_values($notification->getCc()), array_values($notification->getBcc()));
        if (! empty($to)) {
            foreach (array_chunk($to, 1000) as $to_chunk) {
                $result = new Result('mailgun', $this->domain);

                $data = $postData;
                $data['to'] = $to_chunk;

                $res = $this->mailgun->sendMessage($this->domain, $data, $message->toString());
                if ($res->http_response_code == 200) {
                    $success[] = $res;
                } else {
                    $result->setResult(Result::FAIL);
                    $failed[] = $res;
                }

                $result->setResponse($res);
                $notification->addResult($result);
            }

            if (count($success) === 0) {
                throw new NotificationFailedException("Sending failed for message {$notification->getSubject()}", $failed);
            }
        }
    }
}
