<?php

namespace Fazland\Notifire\EventSubscriber\Sms;

use Fazland\Notifire\EventSubscriber\NotifyEventSubscriber;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Exception\IncompleteNotificationException;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;

/**
 * Twilio event subscriber.
 *
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class TwilioHandler extends NotifyEventSubscriber
{
    /**
     * @var \Services_Twilio
     */
    private $twilio;

    /**
     * @param \Services_Twilio $twilio
     */
    public function __construct(\Services_Twilio $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * {@inheritdoc}
     */
    protected function doNotify(NotificationInterface $notification)
    {
        $failedSms = [];

        /** @var Sms $notification */
        $from = $notification->getFrom();
        $content = $notification->getContent();
        $tos = $notification->getTo();

        foreach ($tos as $to) {
            try{
                $this->twilio->account->messages->sendMessage($from, $to, $content);
            }catch(\Exception $e) {
                $failedSms[] = [
                    "to" => $to,
                    "error_message" => $e->getMessage(),
                ];
            }
        }

        if (count($tos) === count($failedSms)) {
            throw new NotificationFailedException("All the sms failed to be send", ['failed_sms' => $failedSms]);
        } elseif (count($failedSms) > 0) {
            throw new IncompleteNotificationException("Some of the sms have not been sent", ['failed_sms' => $failedSms]);
        }
    }

    /**
     * {@inheritdoc}
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
