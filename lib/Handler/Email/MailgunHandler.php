<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Result\Result;
use Mailgun\Mailgun;

/**
 * Mailgun handler
 * It uses swift mailer to build a mime message that will be sent
 * through the Mailgun APIs.
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
     * MailgunHandler constructor.
     *
     * @param Mailgun $mailgun
     * @param string  $domain
     * @param string  $mailerName
     */
    public function __construct(Mailgun $mailgun, string $domain, string $mailerName)
    {
        $this->mailgun = $mailgun;
        $this->domain = $domain;

        parent::__construct($mailerName);
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
        $message = new \Swift_Message($notification->getSubject());
        $message->setFrom($notification->getFrom());

        foreach ($notification->getParts() as $part) {
            $message->attach(new \Swift_MimePart($part->getContent(), $part->getContentType()));
        }

        foreach ($notification->getAttachments() as $attachment) {
            $message->attach(new \Swift_Attachment(
                $attachment->getContent(),
                $attachment->getName(),
                $attachment->getContentType()
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
                $result = new Result('mailgun', $this->getName());

                $data = $postData;
                $data['to'] = $to_chunk;

                $res = $this->mailgun->sendMessage($this->domain, $data, $message->toString());
                if (200 == $res->http_response_code) {
                    $success[] = $res;
                } else {
                    $result->setResult(Result::FAIL);
                    $failed[] = $res;
                }

                $result->setResponse($res);
                $notification->addResult($result);
            }

            if (0 === count($success)) {
                throw new NotificationFailedException("Sending failed for message {$notification->getSubject()}", $failed);
            }
        }
    }
}
