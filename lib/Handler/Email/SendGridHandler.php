<?php

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use \SendGrid;

/**
 * @author Giovanni Albero <giovanni.albero@fazland.com>
 */
class SendGridHandler extends AbstractMailHandler
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var SendGrid
     */
    private $sg;

    /**
     * @param SendGrid $sg
     * @param string $domain
     * @param string $mailerName
     */
    public function __construct(SendGrid $sg, $domain, $mailerName)
    {
        parent::__construct($mailerName);
        $this->domain = $domain;
        $this->sg = $sg;
    }

    /**
     * {@inheritDoc}
     */
    public function notify(NotificationInterface $notification)
    {
        /** @var Email $notification */
        $fromEmails = $notification->getFrom();

        if (count($fromEmails) > 1) {
            throw new \Exception('With sendgrid you can use one from email address');
        }
        $fromEmail = array_pop($fromEmails);

        $from = new SendGrid\Email(null, $fromEmail);
        $subject = $notification->getSubject();
        $mail = new SendGrid\Mail();
        foreach($notification->getParts() as $part) {
            $content = new SendGrid\Content($part->getContentType(), $part->getContent());
            $mail->addContent($content);
        }
        $mail->setFrom($from);
        $mail->setSubject($subject);


        $cc = $notification->getCc();
        $bcc = $notification->getBcc();
        $to = $notification->getTo();

        $pesonalization = new SendGrid\Personalization();
        foreach ($to as $email) {
            $pesonalization->addTo(new SendGrid\Email(null, $email));
        }
        foreach ($cc as $email) {
            $pesonalization->addCc(new SendGrid\Email(null, $email));
        }
        foreach ($bcc as $email) {
            $pesonalization->addBcc(new SendGrid\Email(null, $email));
        }
        $mail->addPersonalization($pesonalization);

        /** @var \SendGrid\Response $response */
        $response = $this->sg->client->mail()->send()->post($mail);

        if ($response->statusCode() > 204) {
            throw new NotificationFailedException("Sending failed via sendgrid with status code " . $response->statusCode() . " and body of response " . $response->body(), [$response]);
        }
    }
}
