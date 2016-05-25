<?php

namespace Fazland\Notifire\EventSubscriber\Sms;

use Fazland\Notifire\EventSubscriber\NotifyEventSubscriber;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class TwilioHandler extends NotifyEventSubscriber
{
    /**
     * @var \Services_Twilio
     */
    private $twilio;

    /**
     * SmsNotificator constructor.
     * @param \Services_Twilio $twilio
     */
    public function __construct(\Services_Twilio $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * @inheritdoc
     */
    protected function doNotify(NotificationInterface $notification)
    {
        $sentSms = [];

        try {
            /** @var Sms $notification */
            $from = $notification->getFrom();
            $content = $notification->getContent();

            foreach ($notification->getTo() as $to) {
                $sentSms[] = $this->twilio->account->messages->sendMessage($from, $to, $content);
            }
        } catch (\Services_Twilio_RestException $e) {
            $errorMessage = "Twilio reported an exception while sending the desired messages. " .
                "These messages were sent:\n";

            foreach ($sentSms as $sms) {
                $errorMessage .=
                    "Twilio Account: $sms->account_sid\n" .
                    "SID: $sms->sid\n" .
                    "From: $sms->from\n" .
                    "To: $sms->to\n" .
                    "Content: $sms->body\n\n"
                ;
            }

            throw new NotificationFailedException($errorMessage, -1, $e);
        }

        if (count($sentSms) === 0) {
            throw new NotificationFailedException("Sms sender service reported that no sms were sent.");
        }
    }

    /**
     * @inheritdoc
     */
    protected function supports(NotificationInterface $notification)
    {
        if (! $notification instanceof Sms) {
            return false;
        }

        $config = $notification->getConfig();
        return $config['provider'] === 'twilio';
    }
}
