<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use SendGrid;

class SendGridHandler extends AbstractMailHandler
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var SendGrid
     */
    private $sendGrid;

    /**
     * @param SendGrid $sendGrid
     * @param string   $domain
     * @param string   $mailerName
     */
    public function __construct(SendGrid $sendGrid, string $domain, string $mailerName)
    {
        parent::__construct($mailerName);
        $this->domain = $domain;
        $this->sendGrid = $sendGrid;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification): void
    {
        /** @var Email $notification */
        $fromEmails = $notification->getFrom();

        if (\count($fromEmails) > 1) {
            throw new \RuntimeException('With SendGrid you can use one from email address');
        }

        $fromEmail = \array_pop($fromEmails);
        $from = new SendGrid\Email(null, $fromEmail);
        $subject = $notification->getSubject();
        $mail = new SendGrid\Mail();
        foreach ($notification->getParts() as $part) {
            $content = new SendGrid\Content($part->getContentType(), $part->getContent());
            $mail->addContent($content);
        }
        $mail->setFrom($from);
        $mail->setSubject($subject);

        $cc = $notification->getCc();
        $bcc = $notification->getBcc();
        $to = $notification->getTo();

        $personalization = new SendGrid\Personalization();
        foreach ($to as $email) {
            $personalization->addTo(new SendGrid\Email(null, $email));
        }
        foreach ($cc as $email) {
            $personalization->addCc(new SendGrid\Email(null, $email));
        }
        foreach ($bcc as $email) {
            $personalization->addBcc(new SendGrid\Email(null, $email));
        }
        $mail->addPersonalization($personalization);

        /** @var SendGrid\Response $response */
        $response = $this->sendGrid->client->mail()->send()->post($mail);

        $statusCode = $response->statusCode();
        if ($statusCode > 204) {
            throw new NotificationFailedException(
                \sprintf(
                    'Sending failed via SendGrid with status code %s and body of response %s',
                    $statusCode,
                    $response->body()
                )[$response])
            ;
        }
    }
}
