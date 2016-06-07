<?php

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Handler\NotificationHandlerInterface;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;

/**
 * Twilio handler
 *
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 */
class TwilioHandler implements NotificationHandlerInterface
{
    /**
     * @var \Services_Twilio
     */
    private $twilio;

    /**
     * @var string
     */
    private $accountName;

    /**
     * @var string
     */
    private $defaultFrom;

    /**
     * @param \Services_Twilio $twilio
     * @param string $name
     */
    public function __construct(\Services_Twilio $twilio, $name = 'default')
    {
        $this->twilio = $twilio;
        $this->accountName = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        $failedSms = [];

        /** @var Sms $notification */
        $from = $notification->getFrom() ?: $this->defaultFrom;
        $content = $notification->getContent();
        $tos = $notification->getTo();

        foreach ($tos as $to) {
            try {
                $this->twilio->account->messages->sendMessage($from, $to, $content);
            } catch (\Exception $e) {
                $failedSms[] = [
                    'to' => $to,
                    'error_message' => $e->getMessage(),
                ];
            }
        }

        if (count($tos) === count($failedSms)) {
            throw new NotificationFailedException('All the sms failed to be send', ['failed_sms' => $failedSms]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(NotificationInterface $notification)
    {
        if (! $notification instanceof Sms) {
            return false;
        }

        $config = $notification->getConfig();

        return $config['provider'] === 'twilio' && $config['account_name'] === $this->accountName;
    }

    /**
     * Set the 'from' default. Used if no from is configured in the Sms object
     *
     * @param string $defaultFrom
     */
    public function setDefaultFrom($defaultFrom)
    {
        $this->defaultFrom = $defaultFrom;
    }
}
